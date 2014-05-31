<?php
global $gBitSystem, $gBitSmarty;
$registerHash = array(
	'package_name' => 'contact',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => FALSE,
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'contact' ) ) {
	$menuHash = array(
		'package_name'  => CONTACT_PKG_NAME,
		'index_url'     => CONTACT_PKG_URL.'index.php',
		'menu_template' => 'bitpackage:contact/menu_contact.tpl',
	);
	$gBitSystem->registerAppMenu( $menuHash );
}
