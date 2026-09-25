<?php
/**
 * Shared Wikidata fetch/apply machinery for ContactWikiIndividual and ContactWikiGroup - identical
 * API-client and claim-extraction logic either way, the only real difference between a person and
 * a group is which biography date properties apply (dob/dod vs formed/disbanded, see
 * biographyDateProps()) and how role-tag suggestions are derived (P106 occupation vs P31 instance-
 * of, left to each class's own picker logic rather than folded in here).
 *
 * A trait, not a shared base class, because the two concrete classes already have divergent real
 * parents (ContactPerson vs ContactBusiness) - PHP has no multiple inheritance, and a trait's own
 * methods still run against $this exactly as if they were declared directly on the using class, so
 * $this->mContentTypeGuid/$this->mContentId are already whatever the concrete constructor set them
 * to. EXTERNAL_ID_PROPS stays a real class constant on each user instead of living here - traits
 * can't declare constants at all.
 *
 * @package contact
 */
namespace Bitweaver\Contact;

use Bitweaver\KernelTools;

trait ContactWikiTrait {

	/**
	 * The Wikidata qid this contact is already linked to (its own stored 'wikidata' xref's
	 * xkey_ext), or null for one that's never been fetched/saved - used by reloadFromWikidata()
	 * when no qid is explicitly passed (the edit.php Reload button case; the add-flow's own initial
	 * Save always passes one explicitly instead, since the xref doesn't exist yet).
	 */
	public function getWikidataQid(): ?string {
		$row = \Bitweaver\Liberty\LibertyContent::lookupXrefByItem( $this->mContentId, 'wikidata', $this->mContentTypeGuid );
		return $row['xkey_ext'] ?? null;
	}

	/**
	 * item => Wikidata property for this content type's own biography date fields - dob/dod (P569/
	 * P570) for an individual, formed/disbanded (P571/P576) for a group. Overridden per class;
	 * reloadFromWikidata() below is entirely generic over whatever this returns, so a third wiki
	 * content type later just needs its own override, not a change here.
	 *
	 * @return array<string,string>
	 */
	abstract protected function biographyDateProps(): array;

	/**
	 * (Re-)fetches this contact's Wikidata entity and applies every derived xref on top of it - the
	 * raw entity json itself, each configured external-id link, this content type's own biography
	 * dates (see biographyDateProps()), a freshly downloaded P18 image, and (when the entity has an
	 * enwiki sitelink) a re-fetched Wikipedia summary as the biography text. Shared by the add-flow's
	 * own initial Save (passing the qid just picked in the fetch step) and edit.php's fisheye-style
	 * 'Reload' action on an already-created contact (passing nothing, so getWikidataQid() supplies
	 * the already-stored one) - same fetch-then-apply cascade either way, just a different qid source.
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

		foreach( static::EXTERNAL_ID_PROPS as $item => $property ) {
			$value = self::stringClaim( $entity, $property );
			if( $value !== null ) {
				$this->upsertXref( $this->mContentId, $item, [ 'xkey_ext' => $value ] );
				$items[] = $item.': '.$value;
			}
		}

		// Biography re-fetch - a Reload should refresh everything Wikidata/Wikipedia can supply,
		// same as the rest of this method. Always overwrites the existing note, same "source wins"
		// behaviour every other item here already has (upsertXref() replaces the stored value
		// unconditionally) - a hand-edited note added since the last Reload would be lost, not
		// merged. LibertyContent::store() directly, not $this->store() (Contact's own override) -
		// this only ever needs to touch the free-text data field, not re-run the address/
		// contact_types/NAME logic Contact::store() layers on top for a full page save. Works
		// identically for both content types - a Wikipedia sitelink exists for a group's own entity
		// just as much as a person's (confirmed live against Fleetwood Mac), unlike TMDb's own
		// biography field, which is person-only and no longer used as a bio source here at all (see
		// fetchTmdbBiography()'s own docblock for why it's kept, just not called from here).
		$wikiTitle = self::wikipediaTitle( $entity );
		if( $wikiTitle !== null ) {
			$bio = self::fetchWikipediaSummary( $wikiTitle );
			if( $bio !== null ) {
				$bioHash = [ 'content_id' => $this->mContentId, 'edit' => self::plainTextToHtmlParagraphs( $bio ) ];
				\Bitweaver\Liberty\LibertyContent::store( $bioHash );
				$items[] = KernelTools::tra( 'Biography' ).' ('.KernelTools::tra( 'Wikipedia' ).')';
			}
		}

		foreach( $this->biographyDateProps() as $item => $property ) {
			$value = self::dateClaim( $entity, $property );
			if( $value !== null ) {
				$this->upsertXref( $this->mContentId, $item, [ 'xkey_ext' => $value ] );
				$items[] = $item.': '.$value;
			}
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
	 * The enwiki sitelink Wikidata's own entity carries (separate from claims entirely - see
	 * contact/MANUAL-WIKI.md's own "What actually answers 'what populates the bio'" section), title
	 * form ("Fleetwood_Mac", underscores not spaces) ready to hand straight to Wikipedia's own REST
	 * summary endpoint. Null when this entity has no English Wikipedia article at all.
	 */
	public static function wikipediaTitle( array $pEntity ): ?string {
		return $pEntity['sitelinks']['enwiki']['title'] ?? null;
	}

	/**
	 * Wikipedia's own REST summary endpoint - a clean lead-paragraph extract, no auth needed, works
	 * identically for a person or a group article. Chosen over TMDb (person/film-cast only, and
	 * TheAudioDB's own free API confirmed dead - both a 404 on well-known artists as of 2026-09-25)
	 * as this package's one generic biography source.
	 */
	public static function fetchWikipediaSummary( string $pTitle ): ?string {
		$context = stream_context_create( [ 'http' => [
			'header'  => "User-Agent: bitweaver-contact-wikidata-lookup/1.0 ( lscesuk@gmail.com )\r\n",
			'timeout' => 15,
		] ] );
		$json = @file_get_contents( 'https://en.wikipedia.org/api/rest_v1/page/summary/'.rawurlencode( $pTitle ), false, $context );
		if( $json === false ) {
			return null;
		}
		$data = json_decode( $json, true );
		$extract = trim( (string)( $data['extract'] ?? '' ) );
		return $extract !== '' ? $extract : null;
	}

	// A MusicBrainz artist id is a bare UUID - distinguishable from a Wikidata Qid or URL, so the
	// same input field can accept either (see resolveWikidataQidFromMusicBrainzArtist()'s own
	// docblock for why this matters).
	public static function extractMusicBrainzArtistId( string $pInput ): ?string {
		return preg_match( '/([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/i', $pInput, $matches )
			? strtolower( $matches[1] ) : null;
	}

	/**
	 * MusicBrainz's own ARTIST-level entity (not a release/album - fisheye's own existing
	 * fetchDiscogsLink() only ever queries the release endpoint, which has no reason to carry this)
	 * commonly links out to the artist's own Wikidata item as a 'wikidata' url-rel - confirmed live
	 * against Fleetwood Mac's own MusicBrainz artist id, resolving to exactly the Q106648 already
	 * found by hand. Lets a MusicBrainz-tagged artist/group be fetched without the user needing to
	 * separately go and search Wikidata at all. Returns null if this artist has no such relation on
	 * MusicBrainz, or the lookup fails outright.
	 */
	public static function resolveWikidataQidFromMusicBrainzArtist( string $pMbArtistId ): ?string {
		$context = stream_context_create( [ 'http' => [
			'header'  => "User-Agent: bitweaver-contact-wikidata-lookup/1.0 ( lscesuk@gmail.com )\r\n",
			'timeout' => 15,
		] ] );
		$json = @file_get_contents(
			"https://musicbrainz.org/ws/2/artist/$pMbArtistId?inc=url-rels&fmt=json", false, $context
		);
		if( $json === false ) {
			return null;
		}
		$data = json_decode( $json, true );
		foreach( $data['relations'] ?? [] as $relation ) {
			if( ( $relation['type'] ?? null ) === 'wikidata' ) {
				$url = $relation['url']['resource'] ?? '';
				if( preg_match( '#/(Q\d+)$#i', $url, $matches ) ) {
					return strtoupper( $matches[1] );
				}
			}
		}
		return null;
	}

	/**
	 * TMDb's own read access token (v4, Bearer auth) - a real secret, so it lives in kernel_config
	 * (contact_tmdb_token, package='contact', editable via admin_contact.php's own Integration
	 * Settings section), never in a committed file, same as fisheye's own fisheye_plex_token.
	 * Returns null (not an error) when unconfigured, so a site with no token set just skips this
	 * step silently rather than failing the whole fetch. Not currently called from
	 * reloadFromWikidata() any more (see that method's own docblock - Wikipedia replaced it as the
	 * generic biography source) - kept for a later film/TV credit bio use, where TMDb's own person
	 * bios are genuinely the better/more detailed source, unlike here.
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
	 * "\n\n") - fine as-is in the add-flow's own plain, non-wysiwyg preview textarea (a <textarea>
	 * always renders \n as a visible line break regardless), but this package's Notes tab (edit.tpl's
	 * own {textarea}) turns wysiwyg on automatically whenever the sitewide bithtml plugin is active -
	 * CKEditor then treats the stored value as HTML *source*, where a bare newline is just collapsed
	 * whitespace, not a paragraph break, so the nice-looking bio flattens into one undifferentiated
	 * block the moment it's actually saved. Converts each blank-line-separated block into its own
	 * real <p>, with any remaining single newline inside one becoming a <br> - the same shape a
	 * normal hand-typed CKEditor note already saves as, so this just matches that existing
	 * convention for an auto-imported one instead of introducing a new format.
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

	// Every wikibase-item-typed value (Q-id) claimed under a given property - shared shape behind
	// both occupationQids() (P106, individual role suggestions) and instanceOfQids() (P31, group
	// type suggestions), not just one of them.
	public static function itemClaimQids( array $pEntity, string $pProperty ): array {
		$qids = [];
		foreach( $pEntity['claims'][$pProperty] ?? [] as $claim ) {
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
