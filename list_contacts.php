<?php
/**
 * @package contact
 * @subpackage functions
 */

require_once '../kernel/includes/setup_inc.php';

use Bitweaver\Contact\ContactPerson;
use Bitweaver\Contact\ContactBusiness;
use Bitweaver\KernelTools;

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_view' );

// Persons and businesses are separate types — each has its own getList().
// The combined display is a view-layer concern: merge, sort, and let the
// template select the row template by content_type_guid.
$personContent   = new ContactPerson();
$businessContent = new ContactBusiness();

$personHash   = $_REQUEST;
$businessHash = $_REQUEST;

$persons    = $personContent->getList( $personHash );
$businesses = $businessContent->getList( $businessHash );

$listcontacts = array_merge( $persons, $businesses );
usort( $listcontacts, fn( $a, $b ) => strcasecmp( $a['title'] ?? '', $b['title'] ?? '' ) );

// listInfo: sum the two counts; use personHash's pagination metadata as base
$listHash = $personHash;
$listHash['cant']              = ( $personHash['cant'] ?? 0 ) + ( $businessHash['cant'] ?? 0 );
$listHash['listInfo']['count'] = $listHash['cant'];

if( $listHash['cant'] == 1 ) {
	KernelTools::bit_redirect( CONTACT_PKG_URL."display_contact.php?content_id=".$listcontacts[0]['content_id'] );
}

$gBitSmarty->assign( 'listcontacts', $listcontacts );
$gBitSmarty->assign( 'listInfo', $listHash['listInfo'] );

$gBitSystem->setBrowserTitle( "View Contacts List" );
$gBitSystem->display( 'bitpackage:contact/list.tpl', NULL, [ 'display_mode' => 'list' ] );
