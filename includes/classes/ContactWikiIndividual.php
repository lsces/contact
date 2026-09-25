<?php
/**
 * A real person credited on a work somewhere in the system (music/film/TV to start, see
 * contact/MANUAL-WIKI.md), backed by data pulled from Wikidata and its own external identity
 * links - content_type_guid='contactwikiindividual', own xref vocabulary (WPxx role tags,
 * external-identity links, biography) registered via rdmcloud's own site-local
 * config/local/xref_schemes/contact.php, not this package's public schema_inc.php.
 *
 * Extends ContactPerson (not Contact directly) so it inherits the same name-storage mechanism
 * (NAME xref, prefix/forename/surname/suffix) - ContactPerson::load()'s own NAME lookup is
 * guid-parameterized already, so registering the same 'name'/NAME shape under this class's own
 * content_type_guid (done in contact.php) is enough on its own, no further code needed for that
 * part.
 *
 * @package contact
 */
namespace Bitweaver\Contact;

class ContactWikiIndividual extends ContactPerson {

	public function __construct( $pContactId = NULL, $pContentId = NULL ) {
		parent::__construct( $pContactId, $pContentId );
		$this->mContentTypeGuid = CONTACTWIKIINDIVIDUAL_CONTENT_TYPE_GUID;
		$this->registerContentType( CONTACTWIKIINDIVIDUAL_CONTENT_TYPE_GUID, [
			'content_type_guid' => CONTACTWIKIINDIVIDUAL_CONTENT_TYPE_GUID,
			'content_name'      => 'Wiki Individual',
			'handler_class'     => 'ContactWikiIndividual',
			'handler_package'   => 'contact',
			'handler_file'      => 'ContactWikiIndividual.php',
			'maintainer_url'    => 'http://lsces.co.uk',
		] );
	}

	/**
	 * Keeps liberty_content.event_time mirroring the editable 'dob' xref - event_time is just a
	 * denormalized convenience for cheap date-based sort/list (the same mechanism Calendar's own
	 * day content already uses), the xref item itself stays the real, editable source of truth.
	 * Catches every write path (a fresh dob added by add_wiki_person.php, or a later edit through
	 * the normal edit_xref.php flow) since upsertXref() delegates to this same storeXref() call
	 * internally - no separate hook needed for either case.
	 */
	public function storeXref( &$pParamHash ): bool {
		$result = parent::storeXref( $pParamHash );
		if( $result && ( $pParamHash['item'] ?? null ) === 'dob' ) {
			$dobValue = trim( (string)( $pParamHash['xkey_ext'] ?? '' ) );
			$eventTime = $dobValue !== '' ? strtotime( $dobValue ) : false;
			if( $eventTime !== false ) {
				$this->mDb->query(
					"UPDATE `".BIT_DB_PREFIX."liberty_content` SET `event_time`=? WHERE `content_id`=?",
					[ $eventTime, $this->mContentId ]
				);
			}
		}
		return $result;
	}
}
