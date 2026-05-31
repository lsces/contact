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

// Load all business-relevant types ($01 and above) for optional checkboxes
$allTypes = $gContent->getXrefSourceList();
$businessTypes = array_filter( $allTypes, fn($t) => $t['item'] !== '$00' && $t['item'] !== '$01' );

$gBitSmarty->assign( 'gContent', $gContent );
$gBitSmarty->assign( 'businessTypes', array_values( $businessTypes ) );
$gBitSmarty->assign( 'errors', $gContent->mErrors );

$gBitSystem->display( 'bitpackage:contact/add_business.tpl', KernelTools::tra( 'Add Business' ), [ 'display_mode' => 'edit' ] );
