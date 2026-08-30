<?php
/**
 * @package contact
 * @subpackage functions
 */

require_once '../kernel/includes/setup_inc.php';

use Bitweaver\Contact\Contact;
use Bitweaver\Contact\ContactPerson;
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

// title is the pipe-encoded prefix|forename|surname|suffix for a person record -
// reformat to the surname-led display form. Businesses already store a plain
// title, untouched. Same as list_people.php: sort order (SQL-level, on the raw
// title column) is by the unformatted pipe-encoded value, not this display
// form - matches list_people.php's own existing behaviour, not a new quirk.
foreach( $listcontacts as &$row ) {
	if( $row['content_type_guid'] === CONTACTPERSON_CONTENT_TYPE_GUID && !empty( $row['title'] ) ) {
		$row['title'] = ContactPerson::formatListName( $row['title'] );
	}
}
unset( $row );

if( $listHash['listInfo']['count'] == 1 ) {
	KernelTools::bit_redirect( CONTACT_PKG_URL."view.php?content_id=".$listcontacts[0]['content_id'] );
}

$gBitSmarty->assign( 'listcontacts', $listcontacts );
$gBitSmarty->assign( 'listInfo', $listHash['listInfo'] );

$gBitSystem->setBrowserTitle( "View Contacts List" );
$gBitSystem->display( 'bitpackage:contact/list.tpl', NULL, [ 'display_mode' => 'list' ] );
