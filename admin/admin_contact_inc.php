<?php

// $Header: /cvsroot/bitweaver/_bit_contact/admin/admin_contact_inc.php,v 1.3 2009/10/01 14:16:59 wjames5 Exp $

// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

use Bitweaver\Contact\ContactType;

$contactTypeMarkers = ContactType::getTypeMarkerList();

// Same shape as fisheye's own admin_fisheye_inc.php ($formGalleryGeneral) - a real secret, so it
// only ever lives in kernel_config (getConfig()/storeConfig()), never a committed file. Used by
// add_wiki_person.php to fetch a person's TMDb biography.
$formContactGeneral = [
	"contact_tmdb_token" => [
		'label' => 'TMDb API Read Access Token',
		'note'  => 'From themoviedb.org/settings/api - the v4 "API Read Access Token" (a long token starting eyJ...), not the shorter v3 "API Key". Used to fetch a person\'s biography when adding a Wiki Individual from a Wikidata entity. Leave blank to skip the biography fetch.',
		'type'  => 'text',
	],
];
$gBitSmarty->assign( 'formContactGeneral', $formContactGeneral );

if( !empty( $_REQUEST['contactGeneralSubmit'] ) ) {
	foreach( $formContactGeneral as $item => $data ) {
		$gBitSystem->storeConfig( $item, trim( (string)( $_REQUEST[$item] ?? '' ) ), CONTACT_PKG_NAME );
	}
}

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

foreach( $contactTypeMarkers as $key => $type ) {
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

