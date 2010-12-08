<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_contact/edit.php,v 1.6 2010/02/08 21:27:22 wjames5 Exp $
 *
 * Copyright (c) 2006 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * @package contact
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_update' );

include_once( CONTACT_PKG_PATH.'lookup_contact_inc.php' );
if( empty( $gContent ) || !is_object( $gContent ) ) {
	$gContent = new Contact();
}

if( !empty( $_REQUEST['xref_id'] ) ) {
	$gContent->loadXref( $_REQUEST['xref_id'] );
}

if (isset($_REQUEST["fCancel"])) {
	if( !empty( $gContent->mContentId ) ) {
		header("Location: ".$gContent->getDisplayUrl() );
	} else {
		header("Location: ".CONTACT_PKG_URL );
	}
	die;
} else if(isset($_REQUEST["fSaveXref"])) {
	if( $gContent->storeXref( $_REQUEST ) ) {
		header("Location: ".$gContent->getDisplayUrl() );
		die;
	} else {
		$xrefInfo = $_REQUEST;
		$xrefInfo['data'] = &$_REQUEST['edit'];
	}
} else if(isset( $_REQUEST["expunge"] ) ) {
	if( $gContent->stepXref( $_REQUEST ) ) {
//		if ( $_REQUEST['expunge'] > 2) {
			header("Location: ".$gContent->getDisplayUrl() );
			die;
//		}
	} 
} 

// formInfo might be set due to a error on submit
if( empty( $xrefInfo ) ) {
	$xrefInfo = &$gContent->mInfo['xref_store']['data'];
}
$gBitSmarty->assign_by_ref( 'xrefInfo', $xrefInfo );
$gBitSmarty->assign_by_ref( 'title', $gContent->mInfo['title'] );
$gBitSmarty->assign_by_ref( 'xref_title', $gContent->mInfo['xref_title'] );

$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );
$gBitSystem->display( 'bitpackage:contact/edit_xref.tpl', 'Edit: ' , array( 'display_mode' => 'edit' ));
?>
