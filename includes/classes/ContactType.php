<?php
/**
 * Contact type registry — loads the sort_order=0 xref groups (person/business subtypes)
 * and exposes them for use in list filters and the contact edit form.
 *
 * @package contact
 */
namespace Bitweaver\Contact;

use Bitweaver\BitBase;
use Bitweaver\Liberty\LibertyXrefType;

class ContactType extends BitBase {
	public $mContactType;

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Return all contact type markers (person + business) as item => title array.
	 *
	 * Each sub-type is queried independently via LibertyXrefType so the two sets
	 * of items are never mixed in a single query.
	 *
	 * @return array<string,string>  e.g. ['$00' => 'Personal', '$04' => 'Supplier', ...]
	 */
	public static function getTypeMarkerList(): array {
		$ret = [];
		foreach ( [ 'contactperson', 'contactbusiness' ] as $guid ) {
			// packageGuid='contact' matters here: a site whose 'type' group was never
			// split per content type (item rows split, group still shared at package
			// level — confirmed on a real site 2026-08-30) needs it to find anything.
			foreach ( ( new LibertyXrefType( $guid, CONTACT_CONTENT_TYPE_GUID ) )->getTypeMarkers() as $m ) {
				$ret[ $m['item'] ] = $m['name'];
			}
		}
		return $ret;
	}

	/**
	 * Merge contact_type_guid from the request into the session store,
	 * persisting the selection as a user preference for registered users.
	 *
	 * @param array $pRequest  Raw $_REQUEST data.
	 * @param array $pStore    Session/list filter hash; modified in place.
	 */
	public function processRequestHash(&$pRequest, &$pStore) {
		global $gBitUser;
		if( !empty( $pRequest["contact_type_guid"] ) ) {
			if( $gBitUser->isRegistered() ) {
				$gBitUser->storePreference( 'contact_default_guids', serialize( $pRequest['contact_type_guid'] ) );
			}
			$pStore['contact_type_guid'] = $pRequest["contact_type_guid"];
		} elseif( !isset( $pStore['content_type_guid'] ) && $gBitUser->getPreference( 'contact_default_guids' ) && $gBitUser->isRegistered() ) {
			$pStore['contact_type_guid'] = unserialize( $gBitUser->getPreference( 'contact_default_guids' ) );
		} elseif( !isset( $pStore['content_type_guid'] ) ) {
			$pStore['contact_type_guid'] = [];
		} elseif( isset( $pRequest["refresh"] ) && !isset( $pRequest["contact_type_guid"] ) ) {
			$pStore['contact_type_guid'] = [];
		}
	}

}
