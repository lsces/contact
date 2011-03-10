<?php
// $Header: /cvsroot/bitweaver/_bit_contact/list_contacts.php,v 1.4 2010/02/08 21:27:22 wjames5 Exp $
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
// Initialization
require_once( '../kernel/setup_inc.php' );
require_once( CONTACT_PKG_PATH.'Contact.php' );

$gBitSystem->isPackageActive('contact', TRUE);

// Now check permissions to access this page
$gBitSystem->verifyPermission('p_read_contact');

$contacts = new Contact();
if ( !isset($_REQUEST['contract'])) {
	$_REQUEST['contract'] = 0;
}

// Get a list of Contracts 
$contracts = $contacts->getContractList( $_REQUEST['contract'] );

$gBitSmarty->assign_by_ref('listInfo', $_REQUEST['listInfo']);
$gBitSmarty->assign_by_ref('list', $contracts);

// Display the template
$gBitSystem->display( 'bitpackage:contact/list_contracts.tpl', NULL, array( 'display_mode' => 'list' ));
?>
