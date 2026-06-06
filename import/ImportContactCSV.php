<?php
/**
 * Contact CSV importer — matches the export_contacts.php column layout.
 *
 * CSV column layout (0-based, header row skipped by loader):
 *   0  title        Contact name — match key (lc.title)
 *   1  type         xref item code: $00 (person), $01–$05 (business types)
 *   2  person_name  Pipe-separated name for $00: prefix|forename|surname|suffix
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

/**
 * Delete any existing xref for ($contentId, $item) then re-insert if either $xkey or
 * $xkeyExt is non-empty.  Values are silently truncated to column limits (xkey C(32),
 * xkey_ext C(250)).
 *
 * @param int    $contentId  liberty_content.content_id.
 * @param string $item       xref item code (e.g. '#P', 'SCREF').
 * @param string $xkey       Short key value (≤ 32 chars after truncation).
 * @param string $xkeyExt    Extended value (≤ 250 chars after truncation).
 * @param int    $xorder     Row order within multiple-valued items (0 for singletons).
 */
function contactCsvUpsertXref( int $contentId, string $item, string $xkey = '', string $xkeyExt = '', int $xorder = 0 ): void {
	global $gBitDb;
	$gBitDb->query(
		"DELETE FROM `" . BIT_DB_PREFIX . "liberty_xref` WHERE `content_id` = ? AND `item` = ?",
		[ $contentId, $item ]
	);
	if( $xkey !== '' || $xkeyExt !== '' ) {
		$gBitDb->associateInsert( BIT_DB_PREFIX . 'liberty_xref', [
			'xref_id'          => $gBitDb->GenID( 'liberty_xref_seq' ),
			'content_id'       => $contentId,
			'item'             => $item,
			'xorder'           => $xorder,
			'xkey'             => $xkey    !== '' ? substr( $xkey,    0, 32  ) : null,
			'xkey_ext'         => $xkeyExt !== '' ? substr( $xkeyExt, 0, 250 ) : null,
			'last_update_date' => $gBitDb->NOW(),
		] );
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

	// --- Find existing or create new via Contact class ---
	$contentId = $gBitDb->getOne(
		"SELECT `content_id` FROM `" . BIT_DB_PREFIX . "liberty_content`
		 WHERE `content_type_guid` = 'contact' AND `title` = ?",
		[ $title ]
	);

	$contact = new Contact( null, $contentId ?: null );
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

	if( !empty( $type ) && $type[0] === '$' ) {
		$pHash['contact_types'] = [ $type ];
		if( $type === '$00' ) {
			$pHash['name'] = $personName;
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
	contactCsvUpsertXref( $contentId, 'SCREF', $scref );
	contactCsvUpsertXref( $contentId, '#P',    $phone, '', 1 );
	contactCsvUpsertXref( $contentId, '#C',    $postcode, $address );
	contactCsvUpsertXref( $contentId, '#F',    $fax, '', 1 );
	contactCsvUpsertXref( $contentId, '#W',    '', $website );
	contactCsvUpsertXref( $contentId, '#E',    '', $email );
	contactCsvUpsertXref( $contentId, 'ACCNO', $accno );

	return $result;
}
