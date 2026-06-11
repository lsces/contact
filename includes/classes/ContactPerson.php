<?php
/**
 * Person contact — extends Contact with content_type_guid='contactperson'.
 *
 * Person-specific xref items ($00 type etc.) are registered at the 'contactperson'
 * level; shared contact fields (addresses, SCREF etc.) live at the 'contact'
 * package level and are picked up via the dual-guid xref pattern.
 *
 * @package contact
 */
namespace Bitweaver\Contact;

class ContactPerson extends Contact {

	public function __construct( $pContactId = NULL, $pContentId = NULL ) {
		parent::__construct( $pContactId, $pContentId );
		$this->mContentTypeGuid = CONTACTPERSON_CONTENT_TYPE_GUID;
		$this->registerContentType( CONTACTPERSON_CONTENT_TYPE_GUID, [
			'content_type_guid' => CONTACTPERSON_CONTENT_TYPE_GUID,
			'content_name'      => 'Person Contact',
			'handler_class'     => 'ContactPerson',
			'handler_package'   => 'contact',
			'handler_file'      => 'ContactPerson.php',
			'maintainer_url'    => 'http://lsces.co.uk',
		] );
		// mPackageGuid='contact' is set automatically by registerContentType()
		// because handler_package('contact') != content_type_guid('contactperson').
	}
}
