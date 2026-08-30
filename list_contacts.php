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

// One combined query across both types, via Contact::getList()'s array
// content_type_guid override (see Contact.php) - a single real LIMIT/offset
// and a single genuine postGetList(), instead of the old approach of running
// ContactPerson::getList() and ContactBusiness::getList() separately and
// merging the two page-slices afterward. That merge is what broke pagination:
// the combined page never had a correct total_pages/current_page of its own,
// only whichever side's listInfo happened to be reused as the base.
$listContent = new Contact();
$listHash = $_REQUEST;
$listHash['content_type_guid'] = [ CONTACTPERSON_CONTENT_TYPE_GUID, CONTACTBUSINESS_CONTENT_TYPE_GUID ];
$listcontacts = $listContent->getList( $listHash );

// title is already the plain surname-led sort/display form straight from the
// DB for a person record, same as a business's own plain title — no
// reformatting needed here (see ContactPerson.php's own docblock). This is
// also what makes the combined SQL ORDER BY above sort correctly across both
// types now, not just paginate correctly.

if( $listHash['listInfo']['count'] == 1 ) {
	KernelTools::bit_redirect( CONTACT_PKG_URL."view.php?content_id=".$listcontacts[0]['content_id'] );
}

$gBitSmarty->assign( 'listcontacts', $listcontacts );
$gBitSmarty->assign( 'listInfo', $listHash['listInfo'] );
// Location/Postcode search boxes here never did anything - find_location/
// find_postcode depended on address_postcode, which no real site populates
// (see Contact::getList()'s own docblock). Own template rather than editing
// display_list_header.tpl itself, which list_people.php/list_businesses.php
// also use unchanged.
$gBitSmarty->assign( 'listFindTpl', 'display_list_header_find.tpl' );

$gBitSystem->setBrowserTitle( "View Contacts List" );
$gBitSystem->display( 'bitpackage:contact/list.tpl', NULL, [ 'display_mode' => 'list' ] );
