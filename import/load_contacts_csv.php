<?php
/**
 * Load contacts from storage/contact/Contacts.csv.
 * Matches on title; updates existing, creates new.
 * Append ?clear=y to delete and re-import all rows.
 *
 * @package contact
 */

namespace Bitweaver\Liberty;

use Bitweaver\KernelTools;

require_once '../../kernel/includes/setup_inc.php';

if( !$gBitSystem->isPackageActive( 'contact' ) ) {
	$gBitSystem->fatalError( 'Contact package not active' );
}
if( !$gBitUser->hasPermission( 'p_contact_admin' ) ) {
	$gBitSystem->fatalError( KernelTools::tra( 'Permission denied' ) );
}

require_once __DIR__ . '/ImportContactCSV.php';

$csvFile = CONTACT_IMPORT_PATH . 'Contacts.csv';
$doClear = ( ( $_REQUEST['clear'] ?? '' ) === 'y' );
$loaded  = 0;
$updated = 0;
$skipped = 0;
$deleted = 0;
$errors  = [];

if( !file_exists( $csvFile ) ) {
	$errors[] = 'CSV file not found: ' . $csvFile;
} else {
	$handle = fopen( $csvFile, 'r' );
	if( $handle === false ) {
		$errors[] = 'Cannot open CSV file.';
	} else {
		$rows   = [];
		$rowNum = 0;
		while( ( $data = fgetcsv( $handle, 0, ',', '"', '' ) ) !== false ) {
			$rowNum++;
			if( $rowNum === 1 ) continue; // skip header
			$rows[] = $data;
		}
		fclose( $handle );

		if( $doClear ) {
			foreach( $rows as $data ) {
				$title = trim( $data[0] ?? '' );
				if( empty( $title ) ) continue;
				$contentId = $gBitDb->getOne(
					"SELECT `content_id` FROM `" . BIT_DB_PREFIX . "liberty_content`
					 WHERE `content_type_guid` = 'contact' AND `title` = ?",
					[ $title ]
				);
				if( $contentId ) {
					$contact = new \Bitweaver\Contact\Contact( (int)$contentId );
					$contact->load();
					$contact->expunge();
					$deleted++;
				}
			}
		}

		foreach( $rows as $idx => $data ) {
			$result   = 
			contactCsvImportRow( $data, $idx + 2 );
			$loaded  += $result['loaded'];
			$updated += $result['updated'];
			$skipped += $result['skipped'];
			$errors   = array_merge( $errors, $result['errors'] );
		}
	}
}

$gBitSmarty->assign( 'loaded',  $loaded );
$gBitSmarty->assign( 'updated', $updated );
$gBitSmarty->assign( 'skipped', $skipped );
$gBitSmarty->assign( 'deleted', $deleted );
$gBitSmarty->assign( 'errors',  $errors );
$gBitSmarty->assign( 'csvFile', $csvFile );

$gBitSystem->display( 'bitpackage:stock/import_results.tpl', KernelTools::tra( 'Import Contacts' ) );
