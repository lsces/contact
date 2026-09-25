<?php
/**
 * A band/orchestra/ensemble/label credited on a work somewhere in the system (music discography to
 * start, see contact/MANUAL-WIKI.md), backed by data pulled from Wikidata and its own external
 * identity links - content_type_guid='contactwikigroup', own xref vocabulary (WBxx role tags,
 * external-identity links, biography) registered via rdmcloud's own site-local
 * config/local/xref_schemes/contact.php, not this package's public schema_inc.php.
 *
 * Extends ContactBusiness (not Contact directly) - a group's own name is stored the same plain way
 * a business's is (liberty_content.title via the 'organisation' field), no NAME-xref name-parts
 * mechanism needed the way ContactWikiIndividual's own ContactPerson parent has.
 *
 * Every generic Wikidata fetch/apply helper (entity fetch, claim extraction, reloadFromWikidata()
 * itself) lives on ContactWikiTrait, shared with ContactWikiIndividual - see that trait's own
 * docblock for why a trait rather than a shared base class. This class supplies the group-specific
 * pieces: the WBxx type-suggestion map, formed/disbanded as this content type's own biography
 * dates, and the formed->event_time mirror.
 *
 * @package contact
 */
namespace Bitweaver\Contact;

class ContactWikiGroup extends ContactBusiness {
	use ContactWikiTrait;

	// item => Wikidata property, matching contact.php's own 'external' group item set exactly -
	// identical list to ContactWikiIndividual's own (a group can have entries under any of these
	// just as validly as a person can, e.g. Discogs/MusicBrainz artist pages exist for groups too).
	const EXTERNAL_ID_PROPS = [
		'imdb'           => 'P345',
		'tmdb'           => 'P4985',
		'tvdb'           => 'P7920',
		'musicbrainz'    => 'P434',
		'discogs_artist' => 'P1953',
		'viaf'           => 'P214',
		'openlibrary'    => 'P648',
		'official_site'  => 'P856',
	];

	// Curated, not a mirror of Wikidata's own P31 (instance of) list - only the one mapping actually
	// confirmed live against a real entity so far (Fleetwood Mac, Q106648 -> P31 Q215380 "musical
	// group"). Orchestra/Choir/Ensemble/Production Company/Record Label's own Q-ids are deliberately
	// NOT guessed here - add them once confirmed the same way, rather than risk a wrong mapping;
	// until then those WBxx codes just show unticked, same as any unmapped WPxx code already does.
	const GROUP_TYPE_MAP = [
		'Q215380' => 'WB01', // musical group
	];

	public function __construct( $pContactId = NULL, $pContentId = NULL ) {
		parent::__construct( $pContactId, $pContentId );
		$this->mContentTypeGuid = CONTACTWIKIGROUP_CONTENT_TYPE_GUID;
		$this->registerContentType( CONTACTWIKIGROUP_CONTENT_TYPE_GUID, [
			'content_type_guid' => CONTACTWIKIGROUP_CONTENT_TYPE_GUID,
			'content_name'      => 'Wiki Group',
			'handler_class'     => 'ContactWikiGroup',
			'handler_package'   => 'contact',
			'handler_file'      => 'ContactWikiGroup.php',
			'maintainer_url'    => 'http://lsces.co.uk',
		] );
	}

	protected function biographyDateProps(): array {
		return [ 'formed' => 'P571', 'disbanded' => 'P576' ];
	}

	/**
	 * Keeps liberty_content.event_time mirroring the editable 'formed' xref - same reasoning as
	 * ContactWikiIndividual's own dob mirror, just this content type's own equivalent single date.
	 */
	public function storeXref( &$pParamHash ): bool {
		$result = parent::storeXref( $pParamHash );
		if( $result && ( $pParamHash['item'] ?? null ) === 'formed' ) {
			$formedValue = trim( (string)( $pParamHash['xkey_ext'] ?? '' ) );
			$eventTime = $formedValue !== '' ? strtotime( $formedValue ) : false;
			if( $eventTime !== false ) {
				$this->mDb->query(
					"UPDATE `".BIT_DB_PREFIX."liberty_content` SET `event_time`=? WHERE `content_id`=?",
					[ $eventTime, $this->mContentId ]
				);
			}
		}
		return $result;
	}

	/**
	 * Storage location for this Contact's own downloaded images (Wikidata's P18, currently the
	 * only source) - see ContactWikiIndividual::getExtraImagePath()'s own docblock, identical
	 * reasoning.
	 *
	 * @return string
	 */
	public function getExtraImagePath( string $pRelativePath ): string {
		return CONTACT_IMPORT_PATH.\Bitweaver\Liberty\liberty_mime_get_storage_branch( [ 'attachment_id' => $this->mContentId ] ).$pRelativePath;
	}

	// P31 (instance of) claims on this entity, as Q-ids - see ContactWikiTrait::itemClaimQids() for
	// the shared shape; add_wiki_group.php maps these against GROUP_TYPE_MAP above.
	public static function instanceOfQids( array $pEntity ): array {
		return self::itemClaimQids( $pEntity, 'P31' );
	}
}
