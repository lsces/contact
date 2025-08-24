<?php

// $Header: /cvsroot/bitweaver/_bit_contact/admin/admin_contact_inc.php,v 1.3 2009/10/01 14:16:59 wjames5 Exp $

// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

use Bitweaver\Contact\ContactType;

$mTypes = new ContactType();
$mTypes->setup();

$formContactListFeatures = [
	"contact_list_id"            => [
		'label' => 'Contact Number',
	],
	"contact_list_forename"      => [
		'label' => 'Forname',
	],
	"contact_list_surname"       => [
		'label' => 'Surname',
	],
	"contact_list_home_phone"    => [
		'label' => 'Home Phone',
	],
	"contact_list_mobile_phone"  => [
		'label' => 'Mobile Phone',
	],
	"contact_list_email"         => [
		'label' => 'eMail Address',
		'help'  => 'Primary contact email address - additional contact details can be found in the full record',
	],
	"contact_list_edit_details"  => [
		'label' => 'Creation and editing details',
		'help'  => 'Enable the record modification data in the contact list. Useful to allow checking when deatils were last changed.',
	],
	"contact_list_last_modified" => [
		'label' => 'Last Modified',
		'help'  => 'Can be selected to enable filter button, without enabling the details section to allow fast checking of the last contact records that have been modified.',
	],
];
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
if (isset($_REQUEST["contactTypesSelected"])) {
	$gBitSmarty->assign('contactTypesSelected', $contactTypesSelected);
}

