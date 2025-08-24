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

if (isset($_REQUEST["fCancel"])) {
	if( !empty( $gContent->mContentId ) ) {
		header("Location: ".$gContent->getDisplayUrl() );
	} else {
		header("Location: ".CONTACT_PKG_URL );
	}
	die;
} elseif (isset($_REQUEST["fAddXref"])) {
	$source = $_REQUEST["source"];
	$format = $_REQUEST["format-".$source];
    if ( $format != 'generic' ) {
    	if ( isset( $_REQUEST[$format."xref"] ) ) { $_REQUEST["xref"] = $_REQUEST[$format."xref"]; }
    	if ( isset( $_REQUEST[$format."xkey"] ) ) { $_REQUEST["xkey"] = $_REQUEST[$format."xkey"]; }
    	if ( isset( $_REQUEST[$format."xkey_ext"] ) ) { $_REQUEST["xkey_ext"] = $_REQUEST[$format."xkey_ext"]; }
    }
	if( $gContent->storeXref( $_REQUEST ) ) {
		header("Location: ".$gContent->getDisplayUrl() );
		die;
	} else {
		$xrefInfo = $_REQUEST;
		$xrefInfo['data'] = &$_REQUEST['edit'];
	}
}

if( !isset( $_REQUEST['xref_type'] ) ) $_REQUEST['xref_type'] = 0;

$gBitSystem->setOnloadScript( 'updateContactXrefFormat();' );

// formInfo might be set due to a error on submit
if( empty( $xrefInfo ) ) {
	$xrefInfo = &$gContent->mInfo['xref_store'];
	$xrefInfo['content_id'] = $gContent->mContentId;
	$xrefInfo['xref_type'] = $_REQUEST['xref_type'];
}
$xrefInfo['xref_type_list'] = $gContent->getXrefTypeList( $xrefInfo['xref_type'] );
$xrefInfo['xref_format_list'] = $gContent->getXrefFormatList();

// Don't use ckeditor for text fields '
$gContent->mInfo['format_guid'] = 'text';
// Default dates for creating new record
$xrefInfo['ignore_start_date'] = 'n';
$xrefInfo['start_date'] = $gContent->mDate->getUTCTime();
$xrefInfo['ignore_end_date'] = 'y';

$gBitSmarty->assign( 'xrefInfo', $xrefInfo );
$gBitSmarty->assign( 'title', $gContent->mInfo['title'] );

$gBitSmarty->assign( 'errors', $gContent->mErrors );
$gBitSystem->display( 'bitpackage:contact/add_xref.tpl', 'Edit: ' , array( 'display_mode' => 'edit' ));
