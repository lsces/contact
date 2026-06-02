<?php
/**
 * @package contact
 * @subpackage functions
 */

require_once '../kernel/includes/setup_inc.php';

use Bitweaver\Contact\Contact;
use Bitweaver\KernelTools;

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_view' );

$gContent = new Contact();
$gContent->invokeServices( 'content_list_function', $_REQUEST );

$listHash = $_REQUEST;
$listcontacts = $gContent->getList( $listHash );

if( $listHash['listInfo']['count'] == 1 ) {
	KernelTools::bit_redirect( CONTACT_PKG_URL."display_contact.php?content_id=".$listcontacts[0]['content_id'] );
}

$gBitSmarty->assign( 'listcontacts', $listcontacts );
$gBitSmarty->assign( 'listInfo', $listHash['listInfo'] );

$gBitSystem->setBrowserTitle( "View Contacts List" );
$gBitSystem->display( 'bitpackage:contact/list.tpl', NULL, [ 'display_mode' => 'list' ] );
