<?php
/**
 * Contact CSV importer — matches the export_contacts.php column layout.
 *
 * CSV column layout (0-based, header row skipped by loader):
 *   0  title        Contact name — match key. For a business this is lc.title directly;
 *                   for a person this is the reassembled display name ("Surname, Prefix
 *                   Forename", matching export_contacts.php's output), not the raw
 *                   pipe-encoded lc.title itself.
 *   1  type         xref item code: P01/P02 (person types), B01–B04 (business types) —
 *                   see contact/admin/schema_inc.php's 'type' group for the current list
 *   2  person_name  Pipe-separated name for a person row: prefix|forename|surname|suffix
 *   3  scref        SCREF xkey (stock source reference / short code)
 *   4  phone        #P xkey
 *   5  address      #C xkey_ext (full address text)
 *   6  postcode     #C xkey
 *   7  fax          #F xkey
 *   8  website      #W xkey_ext (URLs exceed xkey C(32))
 *   9  email        #E xkey_ext (addresses exceed xkey C(32))
 *  10  accno        ACCNO xkey
 *
 * Existing contacts (matched by title) are updated in place.
 * New contacts are created via Contact::store() — liberty handles all required fields.
 *
 * @package contact
 */

namespace Bitweaver\Liberty;

use Bitweaver\Contact\Contact;
use Bitweaver\Contact\ContactPerson;
use Bitweaver\Contact\ContactBusiness;

/**
 * Replace any existing xref row(s) for ($contact, $item) with a single fresh one if
 * either $xkey or $xkeyExt is non-empty. Goes through LibertyXref::store()/stepXref()
 * (via Contact::storeXref()/stepXref()) rather than hitting Xref directly.
 *
 * @param \Bitweaver\Contact\Contact $contact  Already loaded (mXrefInfo populated).
 * @param string                     $item     xref item code (e.g. '#P', 'SCREF').
 * @param string                     $xkey     Short key value (truncated to 32 chars).
 * @param string                     $xkeyExt  Extended value (truncated to 250 chars).
 */
function contactCsvUpsertXref( \Bitweaver\Contact\Contact $contact, string $item, string $xkey = '', string $xkeyExt = '' ): void {
	foreach( $contact->mXrefInfo->findByItem( $item ) as $xrefId ) {
		$stepHash = [ 'xref_id' => $xrefId, 'expunge' => 3 ];
		$contact->stepXref( $stepHash );
	}
	if( $xkey !== '' || $xkeyExt !== '' ) {
		$xrefHash = [
			'content_id' => $contact->mContentId,
			'item'       => $item,
			'xkey'       => $xkey    !== '' ? substr( $xkey,    0, 32  ) : '',
			'xkey_ext'   => $xkeyExt !== '' ? substr( $xkeyExt, 0, 250 ) : '',
			'fAddXref'   => 1,
		];
		$contact->storeXref( $xrefHash );
	}
}

/**
 * Import or update a single contact from a CSV row.
 *
 * Looks up the contact by lc.title; creates a new record if not found.
 * Calls Contact::store() then upserts all xref items (SCREF, #P, #C, #F, #W, #E, ACCNO).
 * 10-digit all-digit phone/fax values have a leading zero prepended (Excel strips it).
 *
 * @param  array $row     0-based column values; see file header for column layout.
 * @param  int   $rowNum  1-based row number used in error messages.
 * @return array{loaded:int, updated:int, skipped:int, errors:string[]}
 */
function contactCsvImportRow( array $row, int $rowNum ): array {
	global $gBitDb;

	$result = [ 'loaded' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => [] ];

	$title      = trim( $row[0]  ?? '' );
	$type       = trim( $row[1]  ?? '' );
	$personName = trim( $row[2]  ?? '' );
	$scref      = trim( $row[3]  ?? '' );
	$phone      = trim( $row[4]  ?? '' );
	// Restore leading zero stripped by Excel on 10-digit UK numbers
	if( strlen( $phone ) === 10 && ctype_digit( $phone ) ) {
		$phone = '0' . $phone;
	}
	$address    = trim( $row[5]  ?? '' );
	$postcode   = trim( $row[6]  ?? '' );
	$fax        = trim( $row[7]  ?? '' );
	if( strlen( $fax ) === 10 && ctype_digit( $fax ) ) {
		$fax = '0' . $fax;
	}
	$website    = trim( $row[8]  ?? '' );
	$email      = trim( $row[9]  ?? '' );
	$accno      = trim( $row[10] ?? '' );

	if( empty( $title ) ) {
		$result['skipped']++;
		return $result;
	}

	// --- Find existing or create new via Contact subclass ---
	$isPerson = ( $type !== '' && $type[0] === 'P' );

	// A person's stored title is pipe-encoded, but the CSV's title column is the
	// reassembled display name — compare against that, not a raw match. Small
	// full-scan is fine for a bounded CSV import.
	if( $isPerson ) {
		$contentId = null;
		$candidates = $gBitDb->query(
			"SELECT `content_id`, `title` FROM `" . BIT_DB_PREFIX . "liberty_content` WHERE `content_type_guid` = 'contactperson'"
		);
		while( $candidate = $candidates->fetchRow() ) {
			if( ContactPerson::formatDisplayName( $candidate['title'] ) === $title ) {
				$contentId = $candidate['content_id'];
				break;
			}
		}
	} else {
		$contentId = $gBitDb->getOne(
			"SELECT `content_id` FROM `" . BIT_DB_PREFIX . "liberty_content`
			 WHERE `content_type_guid` IN ('contactbusiness','contact') AND `title` = ?",
			[ $title ]
		);
	}

	$contact = $isPerson ? new ContactPerson( null, $contentId ?: null ) : new ContactBusiness( null, $contentId ?: null );
	if( $contentId ) {
		$contact->load();
	}

	$pHash = [
		'title'       => $title,
		'edit'        => '',
		'format_guid' => 'bithtml',
	];
	if( $contentId ) {
		$pHash['content_id'] = $contentId;
	}

	if( !empty( $type ) && ( $type[0] === 'P' || $type[0] === 'B' ) ) {
		$pHash['contact_types'] = [ $type ];
		if( $isPerson ) {
			// ContactPerson::verify() builds title from these parts, matching the edit
			// form's own fields — 'name' alone is no longer read anywhere downstream.
			[ $pHash['prefix'], $pHash['forename'], $pHash['surname'], $pHash['suffix'] ] =
				array_pad( explode( '|', $personName, 4 ), 4, '' );
		}
	}

	if( !$contact->store( $pHash ) ) {
		$result['skipped']++;
		$result['errors'][] = "Row $rowNum: failed to store '$title': " . implode( ', ', $contact->mErrors ?? [] );
		return $result;
	}

	$contentId ? $result['updated']++ : $result['loaded']++;
	$contentId = $contact->mContentId;

	// --- Remaining xref items ---
	contactCsvUpsertXref( $contact, 'SCREF', $scref );
	contactCsvUpsertXref( $contact, '#P',    $phone );
	contactCsvUpsertXref( $contact, '#C',    $postcode, $address );
	contactCsvUpsertXref( $contact, '#F',    $fax );
	contactCsvUpsertXref( $contact, '#W',    '', $website );
	contactCsvUpsertXref( $contact, '#E',    '', $email );
	contactCsvUpsertXref( $contact, 'ACCNO', $accno );

	return $result;
}
