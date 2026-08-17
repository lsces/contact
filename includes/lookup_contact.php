<?php
/**
 * JSON autocomplete endpoint — returns contacts matching ?q= by title or SCREF short name.
 *
 * @package contact
 */

namespace Bitweaver\Contact;

require_once '../../kernel/includes/setup_inc.php';

global $gBitDb, $gBitUser;

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

$like = '%'.strtolower( $q ).'%';

$rows = $gBitDb->getArray(
	"SELECT FIRST 30 lc.content_id, lc.title,
		(SELECT FIRST 1 sx.xkey FROM ".BIT_DB_PREFIX."liberty_xref sx
		 WHERE sx.content_id=lc.content_id AND sx.item='SCREF') AS scref
	 FROM ".BIT_DB_PREFIX."liberty_content lc
	 WHERE lc.content_type_guid IN (".implode( ',', array_fill( 0, count( $types ), '?' ) ).")
	   AND (LOWER(lc.title) LIKE ? OR EXISTS (
		SELECT 1 FROM ".BIT_DB_PREFIX."liberty_xref sx
		WHERE sx.content_id=lc.content_id AND sx.item='SCREF' AND LOWER(sx.xkey) LIKE ?
	   ))
	 ORDER BY lc.title",
	[ ...$types, $like, $like ]
);

header( 'Content-Type: application/json' );
echo json_encode( array_values( $rows ?? [] ) );
exit;
