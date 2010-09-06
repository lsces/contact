<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_contact/display_contact.php,v 1.7 2010/02/08 21:27:22 wjames5 Exp $
 *
 * Copyright (c) 2006 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 *
 * @package contact
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );

include_once( CONTACT_PKG_PATH.'Contact.php' );

$gBitSystem->verifyPackage( 'contact' );

$gBitSystem->verifyPermission( 'p_contact_view' );

if( !empty( $_REQUEST['contact_id'] ) ) {
	$gContact = new Contact( $_REQUEST['contact_id'] );
	$gContact->load();
	$gContact->loadXrefList();
} else {
	$gContact = new Contact();
}

$gBitSmarty->assign_by_ref( 'contactInfo', $gContact->mInfo );
//if ( $gContact->isValid() ) {
	$gBitSystem->setBrowserTitle("Contact Information");
	$gBitSystem->display( 'bitpackage:contact/show_contact.tpl');
//} else {
//	header ("location: ".CONTACT_PKG_URL."index.php");
//	die;
//}
?>
