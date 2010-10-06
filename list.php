<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_contact/list.php,v 1.5 2010/02/08 21:27:22 wjames5 Exp $
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

include_once( CONTACT_PKG_PATH.'Contact.php' );

$gBitSystem->verifyPackage( 'contact' );

$gBitSystem->verifyPermission( 'p_contact_view' );

$gContact = new Contact( );

if( !empty( $_REQUEST["find_org"] ) ) {
	$_REQUEST["find_name"] = '';
	$_REQUEST["sort_mode"] = 'organisation_asc';
} else if( empty( $_REQUEST["sort_mode"] ) ) {
	$_REQUEST["sort_mode"] = 'surname_asc';
	$_REQUEST["find_name"] = 'a';
}

//$contact_type = $gContent->getContactsTypeList();
//$gBitSmarty->assign_by_ref('contact_type', $contact_type);
$listHash = $_REQUEST;
// Get a list of matching contact entries
$listcontacts = $gContact->getContactList( $listHash );

$gBitSmarty->assign_by_ref( 'listcontacts', $listcontacts );
$gBitSmarty->assign_by_ref( 'listInfo', $listHash['listInfo'] );

$gBitSystem->setBrowserTitle("View Contacts List");
// Display the template
$gBitSystem->display( 'bitpackage:contact/list.tpl', NULL, array( 'display_mode' => 'list' ));

?>
