<?php
/**
 * JSON autocomplete endpoint — returns contacts matching ?q= by title or SCREF short name.
 *
 * @package contact
 */

namespace Bitweaver\Contact;

use Bitweaver\Liberty\LibertyContent;

require_once '../../kernel/includes/setup_inc.php';

global $gBitUser;

if( !$gBitUser->hasPermission( 'p_contact_view' ) ) {
	header( 'Content-Type: application/json' );
	echo '[]';
	exit;
}

$q = trim( $_GET['q'] ?? '' );
if( strlen( $q ) < 2 ) {
	header( 'Content-Type: application/json' );
	echo '[]';
	exit;
}

// Optional ?type= narrows to a single content_type_guid (e.g. 'contactperson' for a
// picker that should only ever offer people, not businesses) — defaults to both.
$type = $_GET['type'] ?? '';
$types = in_array( $type, [ 'contactperson', 'contactbusiness' ], true )
	? [ $type ]
	: [ 'contactperson', 'contactbusiness' ];

// title is already the plain surname-led display form straight from the DB
// for a person record (see ContactPerson.php's own docblock), same as a
// business's own plain title — no reformatting needed. Still queried per
// type rather than one combined IN() call, in case a caller-specific tweak
// (e.g. a person-only vs business-only label difference) is ever needed here.
$rows = [];
foreach( $types as $searchType ) {
	foreach( LibertyContent::lookupTitles( [ $searchType ], $q, 'SCREF' ) as $row ) {
		$row['scref'] = $row['xkey'];
		unset( $row['xkey'] );
		$rows[] = $row;
	}
}
usort( $rows, fn( $a, $b ) => strcasecmp( $a['title'], $b['title'] ) );
$rows = array_slice( $rows, 0, 30 );

header( 'Content-Type: application/json' );
echo json_encode( $rows );
exit;
