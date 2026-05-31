<?php
/**
 * @package contact
 * @subpackage functions
 */

use Bitweaver\Contact\Contact;
use Bitweaver\KernelTools;

require_once '../kernel/includes/setup_inc.php';

global $gBitSystem, $gBitSmarty, $gBitUser;

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_update' );

$gContent = new Contact();

if( !empty( $_REQUEST['fCancel'] ) ) {
	KernelTools::bit_redirect( CONTACT_PKG_URL );
	die;
}

if( !empty( $_REQUEST['fSaveContact'] ) ) {
	$_REQUEST['contact_types'] = [ '$00' ];
	if( $gContent->store( $_REQUEST ) ) {
		KernelTools::bit_redirect( CONTACT_PKG_URL.'edit.php?content_id='.$gContent->mContentId );
		die;
	}
}

$gBitSmarty->assign( 'gContent', $gContent );
$gBitSmarty->assign( 'errors', $gContent->mErrors );

$gBitSystem->display( 'bitpackage:contact/add_person.tpl', KernelTools::tra( 'Add Person' ), [ 'display_mode' => 'edit' ] );
