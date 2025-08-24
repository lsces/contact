<?php

use Bitweaver\KernelTools;
/**
 * $Header: $
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
$gBitSystem->verifyPackage( 'form' );
// $gBitSystem->verifyPermission( 'p_contact_view' );

include_once CONTACT_PKG_INCLUDE_PATH . 'lookup_contact_inc.php';
use Bitweaver\Form\Form;

global $gBitSystem, $gBitSmarty;

$gForm = new Form( 1, NULL );
$gForm->getFieldList( $_REQUEST );
$gBitSmarty->assign( '$gContent->mInfo', $_REQUEST );
//$gTemplate->getTemplateList();

$gBitSmarty->assign( 'formInfo', $gForm->mForm );

// Process return
if (isset($_REQUEST["fCancel"])) {
	if( !empty( $gContent->mContentId ) ) {
		KernelTools::bit_redirect( $gContent->getDisplayUrl() );
	} else {
		KernelTools::bit_redirect( CONTACT_PKG_URL );
	}
} elseif (isset($_REQUEST["form"])) {
	if( $gForm->storeForm( $_REQUEST, $gContent ) ) {
		KernelTools::bit_redirect( $gContent->getDisplayUrl() );
	} else {
		$formInfo = $_REQUEST;
		$formInfo['data'] = &$_REQUEST['edit'];
	}
} 

// formInfo might be set due to a error on submit
if( empty( $formInfo ) ) {
//	$formInfo = &$gContent->mInfo;
}

$gBitSystem->display( 'bitpackage:contact/employment.tpl', 'Edit: ' , array( 'display_mode' => 'edit' ));
