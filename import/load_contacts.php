<?php
/*
 * Created on 5 Jan 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

// Initialization
require_once( '../../kernel/setup_inc.php' );
require_once(CONTACT_PKG_PATH.'Contact.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'contact' );

// Now check permissions to access this page
$gBitSystem->verifyPermission('p_contact_admin' );

$contact = new Contact();

$contact->DataExpunge();

$row = 0;

$handle = fopen("../data/clientdatabase.csv", "r");
if ( $handle == FALSE) {
	$row = -999;
} else {
	while (($data = fgetcsv($handle, 800, ",")) !== FALSE) {
    	if ( $row ) $contact->ContactRecordLoad( $data );
    	$row++;
	}
	fclose($handle);
}

$gBitSmarty->assign( 'golden', $row );

$gBitSystem->display( 'bitpackage:contacts/load_contacts.tpl', tra( 'Load results: ' ) );
?>
