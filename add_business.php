<?php
/**
 * @package contact
 * @subpackage functions
 */

use Bitweaver\Contact\ContactBusiness;
use Bitweaver\KernelTools;

require_once '../kernel/includes/setup_inc.php';

global $gBitSystem, $gBitSmarty, $gBitUser;

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_update' );

$gContent = new ContactBusiness();

if( !empty( $_REQUEST['fCancel'] ) ) {
	KernelTools::bit_redirect( CONTACT_PKG_URL );
	die;
}

if( !empty( $_REQUEST['fSaveContact'] ) ) {
	$types = [];
	if( !empty( $_REQUEST['contact_types'] ) && is_array( $_REQUEST['contact_types'] ) ) {
		foreach( $_REQUEST['contact_types'] as $type ) {
			if( $type !== '$00' ) {
				$types[] = $type;
			}
		}
	}
	$_REQUEST['contact_types'] = $types;
	if( $gContent->store( $_REQUEST ) ) {
		KernelTools::bit_redirect( CONTACT_PKG_URL.'edit.php?content_id='.$gContent->mContentId );
		die;
	}
}

// ContactBusiness type markers are $02+ only ($00/$01 live under contactperson/deprecated)
$businessTypes = $gContent->getXrefSourceList();

$gBitSmarty->assign( 'gContent', $gContent );
$gBitSmarty->assign( 'businessTypes', array_values( (array)$businessTypes ) );
$gBitSmarty->assign( 'errors', $gContent->mErrors );

$gBitSystem->display( 'bitpackage:contact/add_business.tpl', KernelTools::tra( 'Add Business' ), [ 'display_mode' => 'edit' ] );
