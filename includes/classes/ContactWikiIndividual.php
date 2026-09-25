<?php
/**
 * A real person credited on a work somewhere in the system (music/film/TV to start, see
 * contact/MANUAL-WIKI.md), backed by data pulled from Wikidata and its own external identity
 * links - content_type_guid='contactwikiindi', own xref vocabulary (WPxx role tags,
 * external-identity links, biography) registered via rdmcloud's own site-local
 * config/local/xref_schemes/contact.php, not this package's public schema_inc.php.
 *
 * Extends ContactPerson (not Contact directly) so it inherits the same name-storage mechanism
 * (NAME xref, prefix/forename/surname/suffix) - ContactPerson::load()'s own NAME lookup is
 * guid-parameterized already, so registering the same 'name'/NAME shape under this class's own
 * content_type_guid (done in contact.php) is enough on its own, no further code needed for that
 * part.
 *
 * Every generic Wikidata fetch/apply helper (entity fetch, claim extraction, reloadFromWikidata()
 * itself) lives on ContactWikiTrait, shared with ContactWikiGroup - see that trait's own docblock
 * for why a trait rather than a shared base class. This class supplies the individual-specific
 * pieces: the WPxx occupation-suggestion map, dob/dod as this content type's own biography dates,
 * and the dob->event_time mirror.
 *
 * @package contact
 */
namespace Bitweaver\Contact;

use Bitweaver\KernelTools;

class ContactWikiIndividual extends ContactPerson {
	use ContactWikiTrait;

	// item => Wikidata property, matching contact.php's own 'external' group item set exactly.
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

	// Curated, not a mirror of Wikidata's own occupation (P106) list - only the occupations that
	// map onto a role this system actually credits someone with on a work (fisheye's own
	// director/writer/composer/star items, plus WPxx's own Artist/Performer music roles).
	// Anything else in a person's P106 list (recording artist, singer-songwriter,
	// businessperson, ...) is simply not represented here rather than actively filtered - the
	// picker still shows every WPxx code, ticked or not.
	const OCCUPATION_MAP = [
		'Q10800557' => 'WP01', // film actor
		'Q33999'    => 'WP01', // actor
		'Q2526255'  => 'WP02', // film director
		'Q36834'    => 'WP03', // composer
		'Q753110'   => 'WP03', // songwriter
		'Q177220'   => 'WP04', // singer
		'Q639669'   => 'WP06', // musician
		'Q28389'    => 'WP07', // screenwriter
	];

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

	protected function biographyDateProps(): array {
		return [ 'dob' => 'P569', 'dod' => 'P570' ];
	}

	/**
	 * Keeps liberty_content.event_time mirroring the editable 'dob' xref - event_time is just a
	 * denormalized convenience for cheap date-based sort/list (the same mechanism Calendar's own
	 * day content already uses), the xref item itself stays the real, editable source of truth.
	 * Catches every write path (a fresh dob added by the add-flow, a later reloadFromWikidata()
	 * refresh, or a manual edit through the normal edit_xref.php flow) since upsertXref() delegates
	 * to this same storeXref() call internally - no separate hook needed for any of them.
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

	/**
	 * Storage location for this Contact's own downloaded images (Wikidata's P18, currently the
	 * only source) - CONTACT_IMPORT_PATH is contact's own existing STORAGE_PKG_PATH.'contact/'
	 * convention (see includes/bit_setup_inc.php), bucketed the same way fisheye's own
	 * getImageStorageBranchPath() already does via the shared liberty_mime_get_storage_branch()
	 * helper, so this doesn't end up as one flat directory of every Contact's files. Always
	 * nginx-writable by construction, unlike an external media tree would be.
	 *
	 * @return string
	 */
	public function getExtraImagePath( string $pRelativePath ): string {
		return CONTACT_IMPORT_PATH.\Bitweaver\Liberty\liberty_mime_get_storage_branch( [ 'attachment_id' => $this->mContentId ] ).$pRelativePath;
	}

	// P106 (occupation) claims on this entity, as Q-ids - see ContactWikiTrait::itemClaimQids()
	// for the shared shape; add_wiki_person.php maps these against OCCUPATION_MAP above.
	public static function occupationQids( array $pEntity ): array {
		return self::itemClaimQids( $pEntity, 'P106' );
	}
}
