<?php
/**
 * @version $Header$
 * @package contact
 * @subpackage functions
 */

/**
 * Initialization
 */
use Bitweaver\BitBase;
use Bitweaver\Contact\Contact;
//require_once( TASKS_PKG_PATH.'Tasks.php');

// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
	if( empty( $gContent ) || !is_object( $gContent ) ) {
		if( BitBase::verifyId( $_REQUEST['content_id'] ?? 0 ) ) {
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
	$gBitSmarty->assign( 'taskInfo', $gTask->mInfo );
}
*/
