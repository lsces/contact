<?php
/**
 * @version $Header$
 * @package contact
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( CONTACT_PKG_PATH.'Contact.php');
//require_once( TASKS_PKG_PATH.'Tasks.php');
require_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

	// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
	if( empty( $gContent ) || !is_object( $gContent ) ) {
		if( @BitBase::verifyId( $_REQUEST['content_id'] ) ) {
			$gContent = new Contact( NULL, $_REQUEST['content_id'] );
			$gContent->load();
		} else {
			$gContent = new Contact();
		}
	
		$gBitSmarty->clearAssign( 'gContent' );
		$gBitSmarty->assign( 'gContent', $gContent );
	}
/*
if( is_object( $gContent ) ) {
	$gTask = new Tasks( NULL, $_REQUEST['content_id'] );
	$gTask->mInfo['tasks'] = $gTask->getList( $_REQUEST );
	$gBitSmarty->assignByRef( 'taskInfo', $gTask->mInfo );
}
*/
?>
