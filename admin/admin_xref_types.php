<?php
// $Header$
require_once( '../../kernel/setup_inc.php' );

include_once( CONTACT_PKG_PATH.'Contact.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_admin' );

if( isset( $_REQUEST["fSubmitAddXrefType"] ) ) {
	$gContent->storeXrefType( $_REQUEST );
	if ( !empty( $gContent->mErrors ) ) {
		$gBitSmarty->assign_by_ref('errors', $gContent->mErrors );
	}
} elseif( !empty( $_REQUEST['fActivateXrefType'] )&& $gContent ) {
	$gContent->activateXrefType();
} elseif( !empty( $_REQUEST['fDeactivateXrefType'] )&& $gContent ) {
	$gContent->deactivateXrefType();
} elseif( !empty( $_REQUEST['fRemoveXrefType'] )&& $gContent ) {
	$gContent->removeXrefType();
} elseif( !empty( $_REQUEST['fRemoveXrefTypeAll'] )&& $gContent ) {
	$gContent->removeXrefType( TRUE );
}

$xref_types = ContactXrefType::getContactXrefTypeList();
$gBitSmarty->assign( 'xref_types', $xref_types );

$gBitSystem->display( 'bitpackage:contact/admin_xref_types.tpl', tra( 'Edit Xref Types' ) , array( 'display_mode' => 'admin' ));
?>
