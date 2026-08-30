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

// getList() returns raw liberty_content.title — for persons that's the pipe-encoded
// prefix|forename|surname|suffix, not display-ready. Reformat to the surname-led form
// so the merged listing still sorts and displays by surname, same as before title
// moved out of a P01 xref row and into lc.title itself.
foreach( $persons as &$personRow ) {
	if( !empty( $personRow['title'] ) ) {
		$personRow['title'] = ContactPerson::formatListName( $personRow['title'] );
	}
}
unset( $personRow );

$listcontacts = array_merge( $persons, $businesses );
usort( $listcontacts, fn( $a, $b ) => strcasecmp( $a['title'] ?? '', $b['title'] ?? '' ) );

// listInfo: sum the two counts; use personHash's pagination metadata as base
$listHash = $personHash;
$listHash['cant']              = ( $personHash['cant'] ?? 0 ) + ( $businessHash['cant'] ?? 0 );
$listHash['listInfo']['count'] = $listHash['cant'];

if( $listHash['cant'] == 1 ) {
	KernelTools::bit_redirect( CONTACT_PKG_URL."view.php?content_id=".$listcontacts[0]['content_id'] );
}

$gBitSmarty->assign( 'listcontacts', $listcontacts );
$gBitSmarty->assign( 'listInfo', $listHash['listInfo'] );

$gBitSystem->setBrowserTitle( "View Contacts List" );
$gBitSystem->display( 'bitpackage:contact/list.tpl', NULL, [ 'display_mode' => 'list' ] );
