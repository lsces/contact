<?php

use Bitweaver\KernelTools;
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
if( empty( $gContent ) || !is_object( $gContent ) ) {
	$gContent = new Contact();
}

if( !empty( $_REQUEST['xref_id'] ) ) {
	$gContent->loadXref( $_REQUEST['xref_id'] );
}
$gContent->stepXref( $_REQUEST );

if(isset($_REQUEST["fSaveXref"])) {
	KernelTools::bit_redirect( $gContent->getDisplayUrl() );
}

// formInfo might be set due to a error on submit
if( empty( $xrefInfo ) ) {
	$xrefInfo = &$gContent->mInfo['xref_store']['data'];
}
if( empty( $xrefInfo['content_id'] ) ) {
	$xrefInfo = $_REQUEST;
	if ( isset($xrefInfo['edit']) ) { 
		$xrefInfo['data'] = $xrefInfo['edit'];
	}
}

// Ensure simple text edit boxes for xref entries
$gContent->mInfo['format_guid'] = 'text';
$gBitSmarty->assign( 'xrefInfo', $xrefInfo );
$gBitSmarty->assign( 'title', $gContent->mInfo['title'] );
$gBitSmarty->assign( 'xref_title', $gContent->mInfo['xref_title'] );

$gBitSmarty->assign( 'errors', $gContent->mErrors );
$gBitSystem->display( 'bitpackage:contact/edit_key_break.tpl', 'Edit: ' , array( 'display_mode' => 'edit' ));
