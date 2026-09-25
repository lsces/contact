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
 * Hosts every Wikidata-fetch/apply helper (add_wiki_person.php's own create flow and edit.php's
 * fisheye-style Reload button both call reloadFromWikidata() - moved here, not left as page-level
 * functions, for exactly that reuse, same reasoning as FisheyeAlbum::reloadTracks()/
 * reloadPlexImages() living on the class rather than edit_album.php itself).
 *
 * @package contact
 */
namespace Bitweaver\Contact;

use Bitweaver\KernelTools;

class ContactWikiIndividual extends ContactPerson {

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

	/**
	 * Keeps liberty_content.event_time mirroring the editable 'dob' xref - event_time is just a
	 * denormalized convenience for cheap date-based sort/list (the same mechanism Calendar's own
	 * day content already uses), the xref item itself stays the real, editable source of truth.
	 * Catches every write path (a fresh dob added by add_wiki_person.php, a later reloadFromWikidata()
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

	/**
	 * The Wikidata qid this contact is already linked to (its own stored 'wikidata' xref's
	 * xkey_ext), or null for one that's never been fetched/saved - used by reloadFromWikidata()
	 * when no qid is explicitly passed (the edit.php Reload button case; add_wiki_person.php's own
	 * initial Save always passes one explicitly instead, since the xref doesn't exist yet).
	 */
	public function getWikidataQid(): ?string {
		$row = \Bitweaver\Liberty\LibertyContent::lookupXrefByItem( $this->mContentId, 'wikidata', CONTACTWIKIINDIVIDUAL_CONTENT_TYPE_GUID );
		return $row['xkey_ext'] ?? null;
	}

	/**
	 * (Re-)fetches this contact's Wikidata entity and applies every derived xref on top of it -
	 * the raw entity json itself, each configured external-id link, dob/dod, and a freshly
	 * downloaded P18 image. Shared by add_wiki_person.php's own initial Save (passing the qid just
	 * picked in the fetch step) and edit.php's fisheye-style 'Reload' action on an already-created
	 * contact (passing nothing, so getWikidataQid() supplies the already-stored one) - same
	 * fetch-then-apply cascade either way, just a different qid source.
	 *
	 * Returns a result array in the same shape FisheyeAlbum::reloadTracks()/reloadPlexImages() use
	 * ('items' => human-readable lines of what was applied, or 'error') so edit_album.tpl's own
	 * display convention can be reused as-is.
	 *
	 * @param string|null $pQid
	 * @return array
	 */
	public function reloadFromWikidata( ?string $pQid = null ): array {
		$qid = $pQid ?: $this->getWikidataQid();
		if( !$qid ) {
			return [ 'error' => KernelTools::tra( 'No Wikidata id known for this contact - fetch one first.' ) ];
		}
		$entity = self::fetchWikidataEntity( $qid );
		if( !$entity ) {
			return [ 'error' => KernelTools::tra( 'Could not fetch that Wikidata entity.' ) ];
		}

		$items = [];

		// upsertXref(), not storeXref() directly - storeXref() always inserts a fresh row unless
		// the caller already knows the xref_id to update, which is exactly the "duplicates every
		// item on a second Reload" bug found live: this method runs against an ALREADY-created
		// contact just as often as a brand new one, so every item here needs the "update the
		// existing row if there is one" lookup upsertXref() does, not a blind insert.
		//
		// 'edit', not 'data' - LibertyXref::verify() only ever populates xref_store['data'] from a
		// param key literally named 'edit' (see liberty/MANUAL.md's own "'edit', not 'data'"
		// section) - a plain 'data' key here is silently ignored.
		$this->upsertXref( $this->mContentId, 'wikidata', [ 'xkey_ext' => $qid, 'edit' => json_encode( $entity ) ] );
		$items[] = KernelTools::tra( 'Wikidata entity data' ).' ('.$qid.')';

		$tmdbId = null;
		foreach( self::EXTERNAL_ID_PROPS as $item => $property ) {
			$value = self::stringClaim( $entity, $property );
			if( $value !== null ) {
				$this->upsertXref( $this->mContentId, $item, [ 'xkey_ext' => $value ] );
				$items[] = $item.': '.$value;
				if( $item === 'tmdb' ) {
					$tmdbId = $value;
				}
			}
		}

		// Biography re-fetch, not just the external ids/dob/dod/image add_wiki_person.php's own
		// initial Save already covered - a Reload should refresh everything Wikidata/TMDb can
		// supply, same as the rest of this method. Always overwrites the existing note, same
		// "Wikidata/TMDb wins" behaviour every other item here already has (upsertXref() replaces
		// the stored value unconditionally) - a hand-edited note added since the last Reload would
		// be lost, not merged; worth knowing before clicking Reload on a contact whose bio has since
		// been touched by hand. LibertyContent::store() directly, not $this->store() (Contact's own
		// override) - this only ever needs to touch the free-text data field, not re-run the
		// address/contact_types/NAME logic Contact::store() layers on top for a full page save.
		if( $tmdbId !== null ) {
			$bio = self::fetchTmdbBiography( $tmdbId );
			if( $bio !== null ) {
				$bioHash = [ 'content_id' => $this->mContentId, 'edit' => self::plainTextToHtmlParagraphs( $bio ) ];
				\Bitweaver\Liberty\LibertyContent::store( $bioHash );
				$items[] = KernelTools::tra( 'Biography' ).' ('.KernelTools::tra( 'TMDb' ).')';
			}
		}

		$dob = self::dateClaim( $entity, 'P569' );
		if( $dob !== null ) {
			$this->upsertXref( $this->mContentId, 'dob', [ 'xkey_ext' => $dob ] );
			$items[] = KernelTools::tra( 'Date of birth' ).': '.$dob;
		}
		$dod = self::dateClaim( $entity, 'P570' );
		if( $dod !== null ) {
			$this->upsertXref( $this->mContentId, 'dod', [ 'xkey_ext' => $dod ] );
			$items[] = KernelTools::tra( 'Date of death' ).': '.$dod;
		}

		$imageFilename = self::imageFilename( $entity );
		if( $imageFilename ) {
			$imagesDir = $this->getExtraImagePath( '' );
			$ext = strtolower( pathinfo( $imageFilename, PATHINFO_EXTENSION ) ) ?: 'jpg';
			$storedName = 'wikidata.'.$ext;
			KernelTools::mkdir_p( $imagesDir );
			if( self::downloadCommonsFile( $imageFilename, $imagesDir.$storedName ) ) {
				$this->upsertXref( $this->mContentId, 'image', [ 'xkey_ext' => $storedName ] );
				$items[] = KernelTools::tra( 'Image' ).': '.$imageFilename;
			}
		}

		return [ 'items' => $items ];
	}

	public static function extractQid( string $pInput ): ?string {
		return preg_match( '/(Q\d+)/i', $pInput, $matches ) ? strtoupper( $matches[1] ) : null;
	}

	public static function fetchWikidataEntity( string $pQid ): ?array {
		$context = stream_context_create( [ 'http' => [
			'header'  => "User-Agent: bitweaver-contact-wikidata-lookup/1.0 ( lscesuk@gmail.com )\r\n",
			'timeout' => 15,
		] ] );
		$json = @file_get_contents( "https://www.wikidata.org/wiki/Special:EntityData/$pQid.json", false, $context );
		if( $json === false ) {
			return null;
		}
		$data = json_decode( $json, true );
		return $data['entities'][$pQid] ?? null;
	}

	/**
	 * TMDb's own read access token (v4, Bearer auth) - a real secret, so it lives in kernel_config
	 * (contact_tmdb_token, package='contact', editable via admin_contact.php's own Integration
	 * Settings section), never in a committed file, same as fisheye's own fisheye_plex_token.
	 * Returns null (not an error) when unconfigured, so a site with no token set just skips this
	 * step silently rather than failing the whole fetch.
	 */
	public static function fetchTmdbBiography( string $pTmdbId ): ?string {
		global $gBitSystem;
		$token = $gBitSystem->getConfig( 'contact_tmdb_token', '' );
		if( $token === '' ) {
			return null;
		}
		$context = stream_context_create( [ 'http' => [
			'header'  => "Authorization: Bearer $token\r\nAccept: application/json\r\n",
			'timeout' => 15,
		] ] );
		$json = @file_get_contents( "https://api.themoviedb.org/3/person/$pTmdbId?language=en-US", false, $context );
		if( $json === false ) {
			return null;
		}
		$data = json_decode( $json, true );
		$bio = trim( (string)( $data['biography'] ?? '' ) );
		return $bio !== '' ? $bio : null;
	}

	/**
	 * TMDb's own biography field is plain text, paragraphs separated by a blank line (a literal
	 * "\n\n") - fine as-is in add_wiki_person.tpl's own plain, non-wysiwyg preview textarea (a
	 * <textarea> always renders \n as a visible line break regardless), but this package's Notes
	 * tab (edit.tpl's own {textarea}) turns wysiwyg on automatically whenever the sitewide bithtml
	 * plugin is active - CKEditor then treats the stored value as HTML *source*, where a bare
	 * newline is just collapsed whitespace, not a paragraph break, so the nice-looking bio flattens
	 * into one undifferentiated block the moment it's actually saved. Converts each blank-line-
	 * separated block into its own real <p>, with any remaining single newline inside one becoming
	 * a <br> - the same shape a normal hand-typed CKEditor note already saves as, so this just
	 * matches that existing convention for an auto-imported one instead of introducing a new format.
	 */
	public static function plainTextToHtmlParagraphs( string $pText ): string {
		$blocks = preg_split( '/\n\s*\n/', trim( $pText ) );
		$blocks = array_filter( array_map( 'trim', $blocks ), fn( $p ) => $p !== '' );
		return implode( '', array_map(
			fn( $p ) => '<p>'.nl2br( htmlspecialchars( $p, ENT_QUOTES, 'UTF-8' ) ).'</p>',
			$blocks
		) );
	}

	// Only string-valued claims (external ids) - P106/P569 etc. are wikibase-item/time typed and
	// handled by their own dedicated helpers below, this one would just return null for those.
	public static function stringClaim( array $pEntity, string $pProperty ): ?string {
		foreach( $pEntity['claims'][$pProperty] ?? [] as $claim ) {
			$value = $claim['mainsnak']['datavalue']['value'] ?? null;
			if( is_string( $value ) ) {
				return $value;
			}
		}
		return null;
	}

	public static function occupationQids( array $pEntity ): array {
		$qids = [];
		foreach( $pEntity['claims']['P106'] ?? [] as $claim ) {
			$id = $claim['mainsnak']['datavalue']['value']['id'] ?? null;
			if( $id ) {
				$qids[] = $id;
			}
		}
		return $qids;
	}

	// Wikidata's own time value is "+YYYY-MM-DDT00:00:00Z" (a leading sign, always) - only the
	// date portion is used, precision (day/month/year-only) isn't checked since a plain date is
	// all liberty_content.event_time can hold anyway.
	public static function dateClaim( array $pEntity, string $pProperty ): ?string {
		foreach( $pEntity['claims'][$pProperty] ?? [] as $claim ) {
			$time = $claim['mainsnak']['datavalue']['value']['time'] ?? null;
			if( $time && preg_match( '/([+-]?\d{4}-\d{2}-\d{2})/', $time, $matches ) ) {
				return ltrim( $matches[1], '+' );
			}
		}
		return null;
	}

	// P18's own value is a bare Commons filename (e.g. "Olivia Newton John (...).jpg"), not a URL
	// - entity-typed like the occupation claims, but a plain string value rather than a
	// wikibase-item reference, so this is its own small helper rather than reusing stringClaim().
	public static function imageFilename( array $pEntity ): ?string {
		foreach( $pEntity['claims']['P18'] ?? [] as $claim ) {
			$value = $claim['mainsnak']['datavalue']['value'] ?? null;
			if( is_string( $value ) && $value !== '' ) {
				return $value;
			}
		}
		return null;
	}

	// Commons' own Special:FilePath redirect resolves a bare filename straight to the image bytes,
	// no need to compute the md5-hash-bucketed upload.wikimedia.org path by hand. Downloaded and
	// stored locally (see getExtraImagePath()), never hotlinked - same reasoning as every other
	// externally-sourced image already saved locally elsewhere in this stack.
	public static function downloadCommonsFile( string $pFilename, string $pDestPath ): bool {
		$context = stream_context_create( [ 'http' => [
			'header'  => "User-Agent: bitweaver-contact-wikidata-lookup/1.0 ( lscesuk@gmail.com )\r\n",
			'timeout' => 20,
			'follow_location' => 1,
		] ] );
		$url = 'https://commons.wikimedia.org/wiki/Special:FilePath/'.rawurlencode( $pFilename );
		$bytes = @file_get_contents( $url, false, $context );
		if( $bytes === false || $bytes === '' ) {
			return false;
		}
		return (bool)file_put_contents( $pDestPath, $bytes );
	}
}
