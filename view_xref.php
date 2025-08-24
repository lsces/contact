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
require_once '../kernel/includes/setup_inc.php';

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_update' );

include_once CONTACT_PKG_INCLUDE_PATH . 'lookup_contact_inc.php';

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
} elseif (isset($_REQUEST["fSaveXref"])) {
	if( $gContent->storeXref( $_REQUEST ) ) {
		header("Location: ".$gContent->getDisplayUrl() );
		die;
	} else {
		$xrefInfo = $_REQUEST;
		$xrefInfo['data'] = &$_REQUEST['edit'];
	}
} 

// formInfo might be set due to a error on submit
if( empty( $xrefInfo ) ) {
	$xrefInfo = &$gContent->mInfo['xref_store']['data'];
}

$gBitSmarty->assign( 'xrefInfo', $xrefInfo );
$gBitSmarty->assign( 'title', $gContent->mInfo['title'] );
$gBitSmarty->assign( 'xref_title', $gContent->mInfo['xref_title'] );

$gBitSmarty->assign( 'errors', $gContent->mErrors );
if( isset($xrefInfo['template']) ) {
	$gBitSystem->display( 'bitpackage:contact/view_xref_'.$xrefInfo['template'].'.tpl', 'View: ' , array( 'display_mode' => 'view' ));
} else {
	$gBitSystem->display( 'bitpackage:contact/view_xref.tpl', 'View: ' , array( 'display_mode' => 'view' ));
}
