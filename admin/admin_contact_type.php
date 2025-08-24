<?php
// $Header$
require_once '../../kernel/includes/setup_inc.php';

use Bitweaver\Contact\Contact;
use Bitweaver\Contact\ContactType;
use Bitweaver\KernelTools;

// Is package installed and enabled
$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_admin' );

if( isset( $_REQUEST["fSubmitAddContactType"] ) ) {
	$gContent->storeTopic( $_REQUEST );
	if ( !empty( $gContent->mErrors ) ) {
		$gBitSmarty->assign('errors', $gContent->mErrors );
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

$gBitSystem->display( 'bitpackage:contact/admin_contact_type.tpl', KernelTools::tra( 'Edit Contact Types' ) , array( 'display_mode' => 'admin' ));
