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

if (isset($_REQUEST["fCancel"])) {
	if( !empty( $gContent->mContentId ) ) {
		header("Location: ".$gContent->getDisplayUrl() );
	} else {
		header("Location: ".CONTACT_PKG_URL );
	}
	die;
} elseif (isset($_REQUEST["fAddXref"])) {
	if( $gContent->storeXref( $_REQUEST ) ) {
		header("Location: ".$gContent->getDisplayUrl() );
	} else {
		$xrefInfo = $_REQUEST;
		$xrefInfo['data'] = &$_REQUEST['edit'];
	}
} 

$_REQUEST['xref_type'] = 10;

// formInfo might be set due to a error on submit
if( empty( $xrefInfo ) ) {
	$xrefInfo = &$gContent->mInfo['xref_store'];
	$xrefInfo['content_id'] = $gContent->mContentId;
	$xrefInfo['xref_type'] = $_REQUEST['xref_type'];
}
$xrefInfo['xref_type_list'] = $gContent->getXrefTypeList( 0, 'address' );
$gContent->mInfo['format_guid'] = 'text';

$gBitSmarty->assign_by_ref( 'xrefInfo', $xrefInfo );
$gBitSmarty->assign_by_ref( 'title', $gContent->mInfo['title'] );

$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );
$gBitSystem->display( 'bitpackage:contact/add_xref_address.tpl', 'Edit: ' , array( 'display_mode' => 'edit' ));
?>
