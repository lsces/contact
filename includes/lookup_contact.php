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

$like = '%'.strtolower( $q ).'%';

$rows = $gBitDb->getArray(
	"SELECT FIRST 30 lc.content_id, lc.title,
		(SELECT FIRST 1 sx.xkey FROM ".BIT_DB_PREFIX."liberty_xref sx
		 WHERE sx.content_id=lc.content_id AND sx.item='SCREF') AS scref
	 FROM ".BIT_DB_PREFIX."liberty_content lc
	 WHERE lc.content_type_guid='contact'
	   AND (LOWER(lc.title) LIKE ? OR EXISTS (
		SELECT 1 FROM ".BIT_DB_PREFIX."liberty_xref sx
		WHERE sx.content_id=lc.content_id AND sx.item='SCREF' AND LOWER(sx.xkey) LIKE ?
	   ))
	 ORDER BY lc.title",
	[ $like, $like ]
);

header( 'Content-Type: application/json' );
echo json_encode( array_values( $rows ?? [] ) );
exit;
