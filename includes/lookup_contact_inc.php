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
use Bitweaver\Liberty\LibertyContent;

// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
	if( empty( $gContent ) || !is_object( $gContent ) ) {
		if( BitBase::verifyId( $_REQUEST['content_id'] ?? 0 ) ) {
			// getLibertyObject returns ContactPerson or ContactBusiness (already loaded)
			$gContent = LibertyContent::getLibertyObject( (int)$_REQUEST['content_id'] );
			if( !( $gContent instanceof Contact ) ) {
				// Fallback: content_id exists but is not a contact type
				$gContent = new Contact( NULL, (int)$_REQUEST['content_id'] );
			}
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
