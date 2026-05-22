<?php
/**
 * @package contact
 * @subpackage classes
 */

namespace Bitweaver\Contact;

use Bitweaver\Liberty\LibertyXrefType;

class ContactXrefType extends LibertyXrefType {

	public static function getContactXrefTypeList( $pOptionHash = NULL ) {
		if( $pOptionHash === NULL ) {
			$pOptionHash = [];
		}
		$pOptionHash['content_type_guid'] = 'contact';
		return LibertyXrefType::getXrefTypeList( $pOptionHash );
	}
}
