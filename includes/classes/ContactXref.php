<?php
/**
 * @package contact
 * @subpackage classes
 */

namespace Bitweaver\Contact;

use Bitweaver\Liberty\LibertyXref;

class ContactXref extends LibertyXref {
	protected $mContentTypeGuid = 'contact';

	public function __construct( $iXrefId = NULL ) {
		parent::__construct( $iXrefId );
	}
}
