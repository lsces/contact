<?php
/**
 * Export all contacts to CSV.
 * One row per contact; up to 3 phones, 2 emails.
 * Address: xkey_ext = full address text, xkey = postcode; postcode lookup adds add1-county where available.
 */

namespace Bitweaver\Liberty;

use Bitweaver\KernelTools;

ob_start();
require_once '../kernel/includes/setup_inc.php';

if( !$gBitSystem->isPackageActive( 'contact' ) ) {
	$gBitSystem->fatalError( 'Contact package not active' );
}
if( !$gBitUser->hasPermission( 'p_contact_view' ) ) {
	$gBitSystem->fatalError( KernelTools::tra( 'Permission denied' ) );
}
ob_clean();

// --- Query 1: basic contact info ---
$sql = "SELECT lc.`content_id`, lc.`title`,
		x00.`xkey_ext`   AS person_name,
		xsc.`xkey`       AS scref
		FROM `" . BIT_DB_PREFIX . "liberty_content` lc
		LEFT JOIN `" . BIT_DB_PREFIX . "liberty_xref` x00 ON x00.`content_id` = lc.`content_id` AND x00.`item` = '\$00'
		LEFT JOIN `" . BIT_DB_PREFIX . "liberty_xref` xsc ON xsc.`content_id` = lc.`content_id` AND xsc.`item` = 'SCREF'
		WHERE lc.`content_type_guid` IN ('contactperson','contactbusiness')
		ORDER BY lc.`title`";

$result = $gBitDb->query( $sql );
$contacts = [];
while( $row = $result->fetchRow() ) {
	$contacts[$row['content_id']] = $row + [
		'types'   => [],
		'phones'  => [],
		'fax'     => '',
		'emails'  => [],
		'website' => '',
		'notes'   => [],
		'con'     => '',
		'address' => null,
		'vat_no'  => '',
		'sage_id' => '',
	];
}

if( empty( $contacts ) ) {
	die( "No contacts found.\n" );
}

// --- Query 2: all active xref items for these contacts ---
$sql = "SELECT x.`content_id`, x.`item`, xi.`cross_ref_title`, xi.`template`,
		x.`xkey`, x.`xkey_ext`, x.`data`, x.`xorder`,
		ap.`add1`, ap.`add2`, ap.`add3`, ap.`add4`, ap.`town`, ap.`county`
		FROM `" . BIT_DB_PREFIX . "liberty_xref` x
		JOIN  `" . BIT_DB_PREFIX . "liberty_xref_item` xi ON xi.`item` = x.`item` AND xi.`content_type_guid` IN ('contact','contactperson','contactbusiness')
		LEFT JOIN `" . BIT_DB_PREFIX . "address_postcode` ap ON ap.`postcode` = x.`xkey`
		WHERE ( x.`end_date` IS NULL OR x.`end_date` > CURRENT_TIMESTAMP )
		AND x.`content_id` IN (" . implode( ',', array_keys( $contacts ) ) . ")
		ORDER BY x.`content_id`, x.`item`, x.`xorder`";

$result = $gBitDb->query( $sql );
while( $row = $result->fetchRow() ) {
	$cid  = $row['content_id'];
	$item = $row['item'];
	if( !isset( $contacts[$cid] ) ) continue;

	if( $item[0] === '$' ) {
		$contacts[$cid]['types'][] = $row['cross_ref_title'];
	} elseif( $item === '#P' ) {
		$contacts[$cid]['phones'][] = [ $row['xkey'] ?? '', $row['data'] ?? '' ];
	} elseif( $item === '#F' ) {
		$contacts[$cid]['fax'] = $row['xkey'] ?? '';
	} elseif( $item === '#E' ) {
		$contacts[$cid]['emails'][] = $row['xkey'] ?? '';
	} elseif( $item === '#W' ) {
		$contacts[$cid]['website'] = $row['xkey'] ?? '';
	} elseif( $item === '0' ) {
		if( !empty( $row['data'] ) ) $contacts[$cid]['notes'][] = $row['data'];
	} elseif( $item === 'CON' ) {
		$contacts[$cid]['con'] = $row['xkey'] ?? '';
	} elseif( $item === 'VAT_NO' ) {
		$contacts[$cid]['vat_no'] = $row['xkey'] ?? '';
	} elseif( $item === 'SAGEID' ) {
		$contacts[$cid]['sage_id'] = $row['xkey'] ?? '';
	} elseif( $row['template'] === 'address' && $contacts[$cid]['address'] === null ) {
		// take first active address only
		$contacts[$cid]['address'] = [
			'type'     => $row['cross_ref_title'],
			'address'  => $row['xkey_ext'] ?? '',   // full address text or house name
			'postcode' => $row['xkey']     ?? '',
			'add1'     => $row['add1']     ?? '',    // from postcode lookup
			'add2'     => $row['add2']     ?? '',
			'add3'     => $row['add3']     ?? '',
			'add4'     => $row['add4']     ?? '',
			'town'     => $row['town']     ?? '',
			'county'   => $row['county']   ?? '',
			'notes'    => $row['data']     ?? '',
		];
	}
}

// --- Output CSV ---
header( 'Content-Type: text/csv; charset=utf-8' );
header( 'Content-Disposition: attachment; filename="contacts_' . date( 'Y-m-d' ) . '.csv"' );

$out = fopen( 'php://output', 'w' );

fputcsv( $out, [
	'content_id', 'title', 'type', 'scref',
	'name_prefix', 'forename', 'surname', 'name_suffix',
	'phone1', 'phone1_notes', 'phone2', 'phone2_notes', 'phone3', 'phone3_notes',
	'fax',
	'email1', 'email2',
	'website',
	'notes', 'contact_person',
	'address_type', 'address', 'postcode', 'add1', 'add2', 'add3', 'add4', 'town', 'county', 'address_notes',
	'vat_no', 'sage_id',
], ',', '"', '' );

foreach( $contacts as $cid => $c ) {
	$name  = $c['person_name'] ? explode( '|', $c['person_name'], 4 ) : [];
	$addr  = $c['address'] ?? [];

	fputcsv( $out, [
		$cid,
		$c['title'],
		implode( ', ', $c['types'] ),
		$c['scref'] ?? '',
		$name[0] ?? '', $name[1] ?? '', $name[2] ?? '', $name[3] ?? '',
		$c['phones'][0][0] ?? '', $c['phones'][0][1] ?? '',
		$c['phones'][1][0] ?? '', $c['phones'][1][1] ?? '',
		$c['phones'][2][0] ?? '', $c['phones'][2][1] ?? '',
		$c['fax'],
		$c['emails'][0] ?? '', $c['emails'][1] ?? '',
		$c['website'],
		implode( ' | ', $c['notes'] ),
		$c['con'],
		$addr['type']     ?? '', $addr['address']  ?? '',
		$addr['postcode'] ?? '',
		$addr['add1']     ?? '', $addr['add2']     ?? '',
		$addr['add3']     ?? '', $addr['add4']     ?? '',
		$addr['town']     ?? '', $addr['county']   ?? '',
		$addr['notes']    ?? '',
		$c['vat_no'],
		$c['sage_id'],
	], ',', '"', '' );
}

fclose( $out );
