<?php
global $gBitSystem, $gBitSmarty, $gBitThemes;
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

$gBitThemes->loadJavascript( CONFIG_PKG_PATH.'js/jquery.innerfade.js', FALSE, 700, FALSE );
$gBitThemes->loadJavascript( CONFIG_PKG_PATH.'js/Spry/SpryCollapsiblePanel.js', FALSE, 701, FALSE );
$gBitThemes->loadJavascript( CONFIG_PKG_PATH.'js/Spry/ValidationTextField.js', FALSE, 702, FALSE );
$gBitThemes->loadJavascript( CONFIG_PKG_PATH.'js/Spry/SpryValidationTextarea.js', FALSE, 703, FALSE );
$gBitThemes->loadJavascript( CONFIG_PKG_PATH.'js/Spry/ValidationSelect.js', FALSE, 704, FALSE );

$gBitThemes->loadCss( CONFIG_PKG_PATH."css/cookieconsent.dark.min.css", TRUE, 700 );
$gBitThemes->loadCss( CONFIG_PKG_PATH."js/Spry/SpryCollapsiblePanel.css", TRUE, 701 );
$gBitThemes->loadCss( CONFIG_PKG_PATH."js/Spry/SpryValidationTextField.css", TRUE, 701 );
$gBitThemes->loadCss( CONFIG_PKG_PATH."js/Spry/SpryValidationTextarea.css", TRUE, 701 );
$gBitThemes->loadCss( CONFIG_PKG_PATH."js/Spry/SpryValidationSelect.css", TRUE, 701 );
