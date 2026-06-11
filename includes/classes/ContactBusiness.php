<?php
/**
 * Business contact — extends Contact with content_type_guid='contactbusiness'.
 *
 * Business-specific xref types ($02+ subtypes) are registered at the 'contactbusiness'
 * level; shared contact fields live at the 'contact' package level.
 *
 * @package contact
 */
namespace Bitweaver\Contact;

class ContactBusiness extends Contact {

	public function __construct( $pContactId = NULL, $pContentId = NULL ) {
		parent::__construct( $pContactId, $pContentId );
		$this->mContentTypeGuid = CONTACTBUSINESS_CONTENT_TYPE_GUID;
		$this->registerContentType( CONTACTBUSINESS_CONTENT_TYPE_GUID, [
			'content_type_guid' => CONTACTBUSINESS_CONTENT_TYPE_GUID,
			'content_name'      => 'Business Contact',
			'handler_class'     => 'ContactBusiness',
			'handler_package'   => 'contact',
			'handler_file'      => 'ContactBusiness.php',
			'maintainer_url'    => 'http://lsces.co.uk',
		] );
		// mPackageGuid='contact' is set automatically by registerContentType()
		// because handler_package('contact') != content_type_guid('contactbusiness').
	}
}
