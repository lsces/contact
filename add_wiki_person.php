<?php
/**
 * Add a ContactWikiIndividual seeded from a Wikidata entity - name, WPxx role-tag suggestions (from
 * a curated occupation lookup, not a raw mirror of Wikidata's own broader occupation list), the
 * external-identity links, and dob/dod, all documented at contact/MANUAL-WIKI.md. Two-step flow:
 * fetch shows an editable, pre-filled version of the normal add_person.php form (nothing is written
 * until Save), Save stores the contact exactly like add_person.php does, then layers the xrefs on
 * top - dob/dod as real xref items, not a direct event_time set, so
 * ContactWikiIndividual::storeXref()'s own mirroring keeps liberty_content.event_time in sync
 * automatically.
 *
 * Deliberately scoped: does not attempt a thumbnail (Contact's own IMG/client_gallery mechanism
 * has no real liberty_xref_item behind it at all right now, so there's nothing safe to wire a
 * fetched image into yet), does not attempt a bio fetch (Wikidata's own sitelinks.enwiki gives
 * the Wikipedia article directly, but turning that into fetched prose is its own follow-up), and
 * does not fetch place-of-birth/place-of-death (P19/P20 are Wikidata items, not plain strings -
 * resolving them to a readable place name needs a further lookup, not added here).
 *
 * @package contact
 * @subpackage functions
 */

use Bitweaver\Contact\ContactWikiIndividual;
use Bitweaver\KernelTools;

require_once '../kernel/includes/setup_inc.php';

global $gBitSystem, $gBitSmarty, $gBitUser;

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_update' );

// Curated, not a mirror of Wikidata's own occupation (P106) list - only the occupations that map
// onto a role this system actually credits someone with on a work (fisheye's own director/writer/
// composer/star items, plus WPxx's own Artist/Performer music roles). Anything else in a person's
// P106 list (recording artist, singer-songwriter, businessperson, ...) is simply not represented
// here rather than actively filtered - the picker still shows every WPxx code, ticked or not.
const WIKI_PERSON_OCCUPATION_MAP = [
	'Q10800557' => 'WP01', // film actor
	'Q33999'    => 'WP01', // actor
	'Q2526255'  => 'WP02', // film director
	'Q36834'    => 'WP03', // composer
	'Q753110'   => 'WP03', // songwriter
	'Q177220'   => 'WP04', // singer
	'Q639669'   => 'WP06', // musician
	'Q28389'    => 'WP07', // screenwriter
];

// item => Wikidata property, matching contact.php's own 'external' group item set exactly.
const WIKI_PERSON_EXTERNAL_ID_PROPS = [
	'imdb'           => 'P345',
	'tmdb'           => 'P4985',
	'tvdb'           => 'P7920',
	'musicbrainz'    => 'P434',
	'discogs_artist' => 'P1953',
	'viaf'           => 'P214',
	'openlibrary'    => 'P648',
	'official_site'  => 'P856',
];

function wiki_person_extract_qid( string $pInput ): ?string {
	return preg_match( '/(Q\d+)/i', $pInput, $matches ) ? strtoupper( $matches[1] ) : null;
}

function wiki_person_fetch_entity( string $pQid ): ?array {
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

// Only string-valued claims (external ids) - P106/P569 etc. are wikibase-item/time typed and
// handled separately below, this helper would just return null for those.
function wiki_person_string_claim( array $pEntity, string $pProperty ): ?string {
	foreach( $pEntity['claims'][$pProperty] ?? [] as $claim ) {
		$value = $claim['mainsnak']['datavalue']['value'] ?? null;
		if( is_string( $value ) ) {
			return $value;
		}
	}
	return null;
}

function wiki_person_occupation_qids( array $pEntity ): array {
	$qids = [];
	foreach( $pEntity['claims']['P106'] ?? [] as $claim ) {
		$id = $claim['mainsnak']['datavalue']['value']['id'] ?? null;
		if( $id ) {
			$qids[] = $id;
		}
	}
	return $qids;
}

// Wikidata's own time value is "+YYYY-MM-DDT00:00:00Z" (a leading sign, always) - only the date
// portion is used, precision (day/month/year-only) isn't checked since a plain date is all
// liberty_content.event_time can hold anyway.
function wiki_person_date_claim( array $pEntity, string $pProperty ): ?string {
	foreach( $pEntity['claims'][$pProperty] ?? [] as $claim ) {
		$time = $claim['mainsnak']['datavalue']['value']['time'] ?? null;
		if( $time && preg_match( '/([+-]?\d{4}-\d{2}-\d{2})/', $time, $matches ) ) {
			return ltrim( $matches[1], '+' );
		}
	}
	return null;
}

// P18's own value is a bare Commons filename (e.g. "Olivia Newton John (...).jpg"), not a URL -
// entity-typed like the occupation claims, but a plain string value rather than a wikibase-item
// reference, so this is its own small helper rather than reusing wiki_person_string_claim().
function wiki_person_image_filename( array $pEntity ): ?string {
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
// stored locally (see ContactWikiIndividual::getExtraImagePath()), never hotlinked - same
// reasoning as every other externally-sourced image already saved locally elsewhere in this
// stack.
function wiki_person_download_commons_file( string $pFilename, string $pDestPath ): bool {
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

$gContent = new ContactWikiIndividual();
$wikiEntity = null;
$wikiQid = null;

if( !empty( $_REQUEST['fCancel'] ) ) {
	KernelTools::bit_redirect( CONTACT_PKG_URL );
	die;
}

if( !empty( $_REQUEST['fFetchWikidata'] ) ) {
	$wikiQid = wiki_person_extract_qid( trim( (string)( $_REQUEST['wikidata_input'] ?? '' ) ) );
	if( !$wikiQid ) {
		$gContent->mErrors[] = KernelTools::tra( 'Not a recognisable Wikidata id or URL.' );
	} else {
		$wikiEntity = wiki_person_fetch_entity( $wikiQid );
		if( !$wikiEntity ) {
			$gContent->mErrors[] = KernelTools::tra( 'Could not fetch that Wikidata entity.' );
			$wikiQid = null;
		}
	}
}

if( !empty( $_REQUEST['fSaveContact'] ) ) {
	// No 'P01' injection here - unlike add_person.php, this isn't a contactperson being tagged
	// Personal, it's a genuinely separate content type (contactwikiindividual) that happens to
	// extend ContactPerson for code reuse. contact_types here is purely the WPxx picker's own
	// selections.
	$_REQUEST['contact_types'] = array_values( (array)( $_REQUEST['contact_types'] ?? [] ) );
	$wikiQid = trim( (string)( $_REQUEST['wikidata_qid'] ?? '' ) ) ?: null;
	$wikiRaw = $_REQUEST['wikidata_raw'] ?? null;

	if( $gContent->store( $_REQUEST ) ) {
		if( $wikiQid && $wikiRaw ) {
			$xrefHash = [ 'content_id' => $gContent->mContentId, 'item' => 'wikidata', 'xkey_ext' => $wikiQid, 'data' => $wikiRaw ];
			$gContent->storeXref( $xrefHash );
		}
		foreach( WIKI_PERSON_EXTERNAL_ID_PROPS as $item => $property ) {
			$value = trim( (string)( $_REQUEST['ext_'.$item] ?? '' ) );
			if( $value !== '' ) {
				$xrefHash = [ 'content_id' => $gContent->mContentId, 'item' => $item, 'xkey_ext' => $value ];
				$gContent->storeXref( $xrefHash );
			}
		}
		// 'dob' as a real xref, not just a raw event_time set - ContactWikiIndividual::storeXref()
		// mirrors it into liberty_content.event_time itself, so the xref stays the one editable
		// source of truth this needs to write.
		$dobValue = trim( (string)( $_REQUEST['dob'] ?? '' ) );
		if( $dobValue !== '' ) {
			$xrefHash = [ 'content_id' => $gContent->mContentId, 'item' => 'dob', 'xkey_ext' => $dobValue ];
			$gContent->storeXref( $xrefHash );
		}
		$dodValue = trim( (string)( $_REQUEST['dod'] ?? '' ) );
		if( $dodValue !== '' ) {
			$xrefHash = [ 'content_id' => $gContent->mContentId, 'item' => 'dod', 'xkey_ext' => $dodValue ];
			$gContent->storeXref( $xrefHash );
		}
		// Wikidata's P18 image, downloaded now rather than at fetch time - only worth the real
		// network fetch once the contact is actually being kept, not on every intermediate
		// fetch/re-render of the form.
		$imageFilename = trim( (string)( $_REQUEST['wikidata_image'] ?? '' ) );
		if( $imageFilename !== '' ) {
			$imagesDir = $gContent->getExtraImagePath( '' );
			$ext = strtolower( pathinfo( $imageFilename, PATHINFO_EXTENSION ) ) ?: 'jpg';
			$storedName = 'wikidata.'.$ext;
			\Bitweaver\KernelTools::mkdir_p( $imagesDir );
			if( wiki_person_download_commons_file( $imageFilename, $imagesDir.$storedName ) ) {
				$xrefHash = [ 'content_id' => $gContent->mContentId, 'item' => 'image', 'xkey_ext' => $storedName, 'fAddXref' => 1 ];
				$gContent->storeXref( $xrefHash );
			}
		}
		KernelTools::bit_redirect( CONTACT_PKG_URL.'edit.php?content_id='.$gContent->mContentId );
		die;
	}
}

// Pre-fill from a successful fetch (GET-then-render step) or fall through to whatever was already
// typed (a failed Save re-renders with the same hidden wikidata_raw/wikidata_qid the form already
// carried, not a fresh fetch).
$wikiExternalIds = [];
$wikiSuggestedTypes = [];
$wikiRawJson = $_REQUEST['wikidata_raw'] ?? null;
$wikiDob = $_REQUEST['dob'] ?? null;
$wikiDod = $_REQUEST['dod'] ?? null;
$wikiImageFilename = $_REQUEST['wikidata_image'] ?? null;
$wikiSitelink = null;

if( $wikiEntity ) {
	$label = $wikiEntity['labels']['en']['value'] ?? '';
	$parts = explode( ' ', trim( $label ) );
	$_REQUEST['surname']  = array_pop( $parts ) ?: '';
	$_REQUEST['forename'] = implode( ' ', $parts );

	foreach( WIKI_PERSON_EXTERNAL_ID_PROPS as $item => $property ) {
		$value = wiki_person_string_claim( $wikiEntity, $property );
		if( $value !== null ) {
			$wikiExternalIds[$item] = $value;
		}
	}
	foreach( wiki_person_occupation_qids( $wikiEntity ) as $qid ) {
		if( isset( WIKI_PERSON_OCCUPATION_MAP[$qid] ) ) {
			// Flag-map, not a list - templates here can't reliably call a bare function like
			// in_array() inside {if} (same Smarty restriction fisheye's own MANUAL.md documents
			// for method_exists()), so membership is checked via plain dot-notation instead.
			$wikiSuggestedTypes[WIKI_PERSON_OCCUPATION_MAP[$qid]] = true;
		}
	}
	$wikiDob = wiki_person_date_claim( $wikiEntity, 'P569' );
	$wikiDod = wiki_person_date_claim( $wikiEntity, 'P570' );
	$wikiImageFilename = wiki_person_image_filename( $wikiEntity );
	$wikiSitelink = $wikiEntity['sitelinks']['enwiki']['url'] ?? null;
	$wikiRawJson = json_encode( $wikiEntity );
}

$wikiImagePreviewUrl = $wikiImageFilename ? 'https://commons.wikimedia.org/wiki/Special:FilePath/'.rawurlencode( $wikiImageFilename ).'?width=200' : null;

$gBitSmarty->assign( 'gContent', $gContent );
$gBitSmarty->assign( 'errors', $gContent->mErrors );
$gBitSmarty->assign( 'wikiQid', $wikiQid );
$gBitSmarty->assign( 'wikiRawJson', $wikiRawJson );
$gBitSmarty->assign( 'wikiExternalIds', $wikiExternalIds );
$gBitSmarty->assign( 'wikiSuggestedTypes', $wikiSuggestedTypes );
$gBitSmarty->assign( 'wikiDob', $wikiDob );
$gBitSmarty->assign( 'wikiDod', $wikiDod );
$gBitSmarty->assign( 'wikiImageFilename', $wikiImageFilename );
$gBitSmarty->assign( 'wikiImagePreviewUrl', $wikiImagePreviewUrl );
$gBitSmarty->assign( 'wikiSitelink', $wikiSitelink );
$gBitSmarty->assign( 'personTypes', $gContent->getAvailableTypeItems() );

$gBitSystem->display( 'bitpackage:contact/add_wiki_person.tpl', KernelTools::tra( 'Add Wiki Individual' ), [ 'display_mode' => 'edit' ] );
