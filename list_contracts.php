<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_contact/list_contacts.php,v 1.4 2010/02/08 21:27:22 wjames5 Exp $
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

$gBitSmarty->assignByRef('listInfo', $_REQUEST['listInfo']);
$gBitSmarty->assignByRef('list', $contracts);

// Display the template
$gBitSystem->display( 'bitpackage:contact/list_contracts.tpl', NULL, array( 'display_mode' => 'list' ));
?>
