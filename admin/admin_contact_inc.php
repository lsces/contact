<?php

// $Header: /cvsroot/bitweaver/_bit_contact/admin/admin_contact_inc.php,v 1.3 2009/10/01 14:16:59 wjames5 Exp $

// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

include_once( CONTACT_PKG_PATH.'Contact.php' );
$mTypes = new ContactType();
$mTypes->setup();

$formContactListFeatures = array(
	"contact_list_id" => array(
		'label' => 'Contact Number',
	),
	"contact_list_forename" => array(
		'label' => 'Forname',
	),
	"contact_list_surname" => array(
		'label' => 'Surname',
	),
	"contact_list_home_phone" => array(
		'label' => 'Home Phone',
	),
	"contact_list_mobile_phone" => array(
		'label' => 'Mobile Phone',
	),
	"contact_list_email" => array(
		'label' => 'eMail Address',
		'help' => 'Primary contact email address - additional contact details can be found in the full record',
	),
	"contact_list_edit_details" => array(
		'label' => 'Creation and editing details',
		'help' => 'Enable the record modification data in the contact list. Useful to allow checking when deatils were last changed.',
	),
	"contact_list_last_modified" => array(
		'label' => 'Last Modified',
		'help' => 'Can be selected to enable filter button, without enabling the details section to allow fast checking of the last contact records that have been modified.',
	),
);
$gBitSmarty->assign( 'formContactListFeatures',$formContactListFeatures );

foreach( $mTypes->mContactType as $key => $type ) {
	$option = 'contact_default_'.$key;
	$contactChecks[] = $option;
	$contactTypeDefaults[$option] = $type;
}
asort($contactTypeDefaults);
$gBitSmarty->assign('contactTypeDefaults', $contactTypeDefaults);

if (isset($_REQUEST["contactlistfeatures"])) {
	
	foreach( $formContactListFeatures as $item => $data ) {
		simple_set_toggle( $item, CONTACT_PKG_NAME );
	}

	foreach( $contactTypeDefaults as $key => $val ) {
		simple_set_toggle_array( 'defaultTypes', $key, CONTACT_PKG_NAME);
	}	
}

foreach( $contactTypeDefaults as $key => $val) {
	if ($gBitSystem->isFeatureActive($key) ){
		$contactTypesSelected[] = $key;
	}
}
$gBitSmarty->assign('contactTypesSelected', $contactTypesSelected);

?>
