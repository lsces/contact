<?php
/*
 * Created on 5 Jan 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

// Initialization
require_once '../kernel/includes/setup_inc.php';
use Bitweaver\Contact\Contact;
use Bitweaver\KernelTools;

// Is package installed and enabled
$gBitSystem->verifyPackage( 'contact' );

// Now check permissions to access this page
$gBitSystem->verifyPermission('p_contact_admin' );

$contact = new Contact();

$contact->SageDataExpunge();

$row = 0;

$handle = fopen("data/customers.csv", "r");
if ( $handle == FALSE) {
	$row = -999;
} else {
	while (($data = fgetcsv($handle, 800, ",")) !== FALSE) {
    	if ( $row ) $contact->SageRecordLoad( $data, 1 );
    	$row++;
	}
	fclose($handle);
}

$gBitSmarty->assign( 'customers', $row );
$row = 0;

$handle = fopen("data/suppliers.csv", "r");
if ( $handle == FALSE) {
	$row = -999;
} else {
	while (($data = fgetcsv($handle, 800, ",")) !== FALSE) {
    	if ( $row ) $contact->SageRecordLoad( $data, 2 );
    	$row++;
	}
	fclose($handle);
}

$gBitSmarty->assign( 'suppliers', $row );

$gBitSystem->display( 'bitpackage:contacts/load_sage_contacts.tpl', KernelTools::tra( 'Load results: ' ) );
