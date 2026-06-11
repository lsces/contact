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
define( 'CONTACT_PKG_CLASS_PATH',   BIT_ROOT_PATH . basename( $pRegisterHash['package_path'] ) . '/includes/classes/');
define( 'CONTACT_PKG_ADMIN_PATH', BIT_ROOT_PATH . basename( $pRegisterHash['package_path'] ) . '/admin/');
define( 'CONTACT_IMPORT_PATH', STORAGE_PKG_PATH . 'contact/' );
$gBitSystem->registerPackage( $pRegisterHash );

if( $gBitSystem->isPackageActive( 'contact' ) ) {
	// Register sub-type content types at startup so getLibertyObject() can resolve them.
	// registerContentType() is a no-op in memory once the row exists in the DB.
	$gLibertySystem->registerContentType( 'contactperson', [
		'content_type_guid' => 'contactperson',
		'content_name'      => 'Person Contact',
		'handler_class'     => 'ContactPerson',
		'handler_package'   => 'contact',
		'handler_file'      => 'ContactPerson.php',
	] );
	$gLibertySystem->registerContentType( 'contactbusiness', [
		'content_type_guid' => 'contactbusiness',
		'content_name'      => 'Business Contact',
		'handler_class'     => 'ContactBusiness',
		'handler_package'   => 'contact',
		'handler_file'      => 'ContactBusiness.php',
	] );

	$menuHash = [
		'package_name'  => CONTACT_PKG_NAME,
		'index_url'     => CONTACT_PKG_URL . 'index.php',
		'menu_template' => 'bitpackage:contact/menu_contact.tpl',
	];
	$gBitSystem->registerAppMenu( $menuHash );
}
