<?php
/**
 * @version $Header$
 * @package contact
 * @subpackage functions
 */

/**
 * Initialization
 */
require_once( '../kernel/setup_inc.php' );

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_update' );

include_once( CONTACT_PKG_PATH.'Contact.php' );
$gContent = new Contact();

$gBitSmarty->assignByRef( 'xref_type_info', $gContent->mInfo);

if( isset( $_REQUEST["fSubmitSaveXrefType"] ) ) {
    $gContent->storeXrefType( $_REQUEST );
	$gContent->loadXrefType();
    header( "Location: " . CONTACT_PKG_URL . "admin/admin_xref_type.php" );
} elseif( isset( $_REQUEST['fRemoveXref'] ) ) {
	$gContent->expungeXrefType();
}

$gBitSystem->display( 'bitpackage:contact/edit_xref_type.tpl' , NULL, array( 'display_mode' => 'edit' ));
?>