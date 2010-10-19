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

$gContent = new Contact( );
$gContent->invokeServices( 'content_list_function', $_REQUEST );

if( empty( $_REQUEST["find_org"] ) ) {
	$_REQUEST["find_name"] = '';
	$_REQUEST["sort_mode"] = 'title_asc';
} 

// Handle the request hash storing into the session.
$gContent->mTypes->processRequestHash($_REQUEST, $_SESSION['contact']);

// Setup which contact types we want to view.
$contactTypes = $gContent->getContactTypes();
if( $gBitUser->hasPermission("p_contact_view_changes") && $_SESSION['contact']['contact_type_guid'] ) {
	$listHash = $_SESSION['contact'];
} else {
	foreach ($contactTypes as $key => $val) {
		if ($gBitSystem->isFeatureActive('contact_default_'.$key)) {
			$listHash['contact_type_guid'][] = $key;
		}
	}
}

$listHash = $_REQUEST;
// Get a list of matching contact entries
$listcontacts = $gContent->getList( $listHash );

$gBitSmarty->assign_by_ref( 'listcontacts', $listcontacts );
$gBitSmarty->assign_by_ref( 'listInfo', $listHash['listInfo'] );

$gBitSystem->setBrowserTitle("View Contacts List");
// Display the template
$gBitSystem->display( 'bitpackage:contact/list.tpl', NULL, array( 'display_mode' => 'list' ));

?>
