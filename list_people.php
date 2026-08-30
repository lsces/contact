<?php
/**
 * @package contact
 */

require_once '../kernel/includes/setup_inc.php';

use Bitweaver\Contact\ContactPerson;
use Bitweaver\KernelTools;

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_view' );

$gContent = new ContactPerson();
$gContent->invokeServices( 'content_list_function', $_REQUEST );
$gContent->mTypes->processRequestHash( $_REQUEST, $_SESSION['contact'] );

$listHash = $_REQUEST;
$listcontacts = $gContent->getList( $listHash );

// title is the pipe-encoded prefix|forename|surname|suffix for a person record —
// reformat to the surname-led display/sort form, same as list_contacts.php.
foreach( $listcontacts as &$personRow ) {
	if( !empty( $personRow['title'] ) ) {
		$personRow['title'] = ContactPerson::formatListName( $personRow['title'] );
	}
}
unset( $personRow );

if( $listHash['listInfo']['count'] == 1 ) {
	KernelTools::bit_redirect( CONTACT_PKG_URL . "view.php?content_id=" . $listcontacts[0]['content_id'] );
}

$gBitSmarty->assign( 'listcontacts', $listcontacts );
$gBitSmarty->assign( 'listInfo', $listHash['listInfo'] );
$gBitSmarty->assign( 'listTitle', KernelTools::tra( 'People' ) );

$gBitSystem->setBrowserTitle( KernelTools::tra( 'People' ) );
$gBitSystem->display( 'bitpackage:contact/list.tpl', NULL, [ 'display_mode' => 'list' ] );
