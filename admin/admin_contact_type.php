<?php
// $Header$
require_once( '../../kernel/setup_inc.php' );

include_once( CONTACT_PKG_PATH.'Contact.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_admin' );

if( isset( $_REQUEST["fSubmitAddContactType"] ) ) {
	$gContent->storeTopic( $_REQUEST );
	if ( !empty( $gContent->mErrors ) ) {
		$gBitSmarty->assign_by_ref('errors', $gContent->mErrors );
	}
} elseif( !empty( $_REQUEST['fActivateTopic'] )&& $gContent ) {
	$gContent->activateTopic();
} elseif( !empty( $_REQUEST['fDeactivateTopic'] )&& $gContent ) {
	$gContent->deactivateTopic();
} elseif( !empty( $_REQUEST['fRemoveTopic'] )&& $gContent ) {
	$gContent->removeTopic();
} elseif( !empty( $_REQUEST['fRemoveTopicAll'] )&& $gContent ) {
	$gContent->removeTopic( TRUE );
}

$contacttype = ContactType::getContactTypeList();
$gBitSmarty->assign( 'contacttype', $contacttype );

$gBitSystem->display( 'bitpackage:contact/admin_contact_type.tpl', tra( 'Edit Contact Types' ) , array( 'display_mode' => 'admin' ));
?>
