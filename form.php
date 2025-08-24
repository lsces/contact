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

$cat_type = BITPAGE_CONTENT_TYPE_GUID;
if(isset($_REQUEST["fSaveForm"])) {
	\Bitweaver\vd($_REQUEST);
} else {

}

// Pro
if (isset($_REQUEST["fCancel"])) {
	if( !empty( $gContent->mContentId ) ) {
		KernelTools::bit_redirect( $gContent->getDisplayUrl() );
	} else {
		KernelTools::bit_redirect( CONTACT_PKG_URL );
	}
} elseif (isset($_REQUEST["fSaveContact"])) {
	if( $gContent->store( $_REQUEST ) ) {
		KernelTools::bit_redirect( $gContent->getDisplayUrl() );
	} else {
		$formInfo = $_REQUEST;
		$formInfo['data'] = &$_REQUEST['edit'];
	}
} 

$gBitSystem->display( 'bitpackage:contact/form_DSInspection.tpl', 'Form: OD01 ' , array( 'display_mode' => 'form' ));
