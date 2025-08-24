<?php
/**
 * @author   lsces <lester@lsces.uk>
 * @version  $Revision$
 * @package  Contact
 * @subpackage functions
 */
global $gBitSystem, $gBitSmarty;
$pRegisterHash = [
	'package_name' => 'contact',
	'package_path' => dirname( dirname( __FILE__ ) ) . '/',
	'homeable'     => false,
];
define( 'CONTACT_PKG_NAME', $pRegisterHash['package_name'] );
define( 'CONTACT_PKG_URL', BIT_ROOT_URL . basename( $pRegisterHash['package_path'] ) . '/' );
define( 'CONTACT_PKG_PATH', BIT_ROOT_PATH . basename( $pRegisterHash['package_path'] ) . '/' );
define( 'CONTACT_PKG_INCLUDE_PATH', BIT_ROOT_PATH . basename( $pRegisterHash['package_path'] ) . '/includes/'); 
$gBitSystem->registerPackage( $pRegisterHash );

if( $gBitSystem->isPackageActive( 'contact' ) ) {
	$menuHash = [
		'package_name'  => CONTACT_PKG_NAME,
		'index_url'     => CONTACT_PKG_URL . 'index.php',
		'menu_template' => 'bitpackage:contact/menu_contact.tpl',
	];
	$gBitSystem->registerAppMenu( $menuHash );
}
