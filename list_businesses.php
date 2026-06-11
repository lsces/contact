<?php
/**
 * @package contact
 */

require_once '../kernel/includes/setup_inc.php';

use Bitweaver\Contact\ContactBusiness;
use Bitweaver\Contact\ContactType;
use Bitweaver\KernelTools;
use Bitweaver\Liberty\LibertyXrefType;

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_view' );

$gContent = new ContactBusiness();
$gContent->invokeServices( 'content_list_function', $_REQUEST );
$gContent->mTypes->processRequestHash( $_REQUEST, $_SESSION['contact'] );

// Business type filter: contactbusiness type markers only (never mix with contactperson)
$businessTypes = ( new LibertyXrefType( CONTACTBUSINESS_CONTENT_TYPE_GUID ) )->getTypeMarkers();
$gBitSmarty->assign( 'contContactTypes', array_column( $businessTypes, 'name', 'item' ) );

$listHash = $_REQUEST;
$listcontacts = $gContent->getList( $listHash );

if( $listHash['listInfo']['count'] == 1 ) {
	KernelTools::bit_redirect( CONTACT_PKG_URL . "display_contact.php?content_id=" . $listcontacts[0]['content_id'] );
}

$gBitSmarty->assign( 'listcontacts', $listcontacts );
$gBitSmarty->assign( 'listInfo', $listHash['listInfo'] );
$gBitSmarty->assign( 'listTitle', KernelTools::tra( 'Businesses' ) );

$gBitSystem->setBrowserTitle( KernelTools::tra( 'Businesses' ) );
$gBitSystem->display( 'bitpackage:contact/list.tpl', NULL, [ 'display_mode' => 'list' ] );
