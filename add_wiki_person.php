<?php
/**
 * Add a ContactWikiIndividual seeded from a Wikidata entity - name, WPxx role-tag suggestions (from
 * ContactWikiIndividual::OCCUPATION_MAP, a curated lookup, not a raw mirror of Wikidata's own
 * broader occupation list), the external-identity links, dob/dod, a downloaded P18 image, and a
 * TMDb-fetched biography, all documented at contact/MANUAL-WIKI.md. Two-step flow: fetch shows an
 * editable, pre-filled version of the normal add_person.php form (nothing is written until Save),
 * Save stores the contact exactly like add_person.php does, then hands off to
 * ContactWikiIndividual::reloadFromWikidata() to lay every xref on top - the same fetch-then-apply
 * cascade edit.php's own 'Reload' action reuses for an already-created contact.
 *
 * The biography pre-fills the same Note field ('edit' -> lc.data) every other fetched value
 * pre-fills its own field, not a separate mechanism - needs contact_tmdb_token set in
 * kernel_config (see ContactWikiIndividual::fetchTmdbBiography()'s own docblock), silently skipped
 * if not configured or TMDb has no bio for this person.
 *
 * Deliberately scoped: does not fetch place-of-birth/place-of-death (P19/P20 are Wikidata items,
 * not plain strings - resolving them to a readable place name needs a further lookup, not added
 * here).
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

$gContent = new ContactWikiIndividual();
$wikiEntity = null;
$wikiQid = null;

if( !empty( $_REQUEST['fCancel'] ) ) {
	KernelTools::bit_redirect( CONTACT_PKG_URL );
	die;
}

if( !empty( $_REQUEST['fFetchWikidata'] ) ) {
	$rawInput = trim( (string)( $_REQUEST['wikidata_input'] ?? '' ) );
	$wikiQid = ContactWikiIndividual::extractQid( $rawInput );
	if( !$wikiQid ) {
		// Not a Wikidata id/URL - try it as a MusicBrainz artist id/URL instead, resolving via that
		// artist's own 'wikidata' url-rel (confirmed live against Fleetwood Mac's own MusicBrainz
		// artist entity - see resolveWikidataQidFromMusicBrainzArtist()'s own docblock) rather than
		// making the user go and search Wikidata separately.
		$mbArtistId = ContactWikiIndividual::extractMusicBrainzArtistId( $rawInput );
		if( $mbArtistId ) {
			$wikiQid = ContactWikiIndividual::resolveWikidataQidFromMusicBrainzArtist( $mbArtistId );
			if( !$wikiQid ) {
				$gContent->mErrors[] = KernelTools::tra( 'That MusicBrainz artist has no linked Wikidata id.' );
			}
		} else {
			$gContent->mErrors[] = KernelTools::tra( 'Not a recognisable Wikidata id/URL or MusicBrainz artist id/URL.' );
		}
	}
	if( $wikiQid ) {
		$wikiEntity = ContactWikiIndividual::fetchWikidataEntity( $wikiQid );
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

	// A separate copy for store(), not a mutation of $_REQUEST['edit'] itself - if store() fails
	// validation (e.g. no name), the form re-renders from $_REQUEST below and should still show the
	// plain, readable text the user was just editing, not the HTML this converts it to for saving.
	$storeHash = $_REQUEST;
	if( !empty( $storeHash['edit'] ) && strip_tags( $storeHash['edit'] ) === $storeHash['edit'] ) {
		// Plain text in, no tags of its own yet - this is the auto-fetched TMDb bio (or anything
		// else typed as plain text), not something already re-edited through the Notes tab's own
		// CKEditor. See ContactWikiIndividual::plainTextToHtmlParagraphs()'s own docblock for why
		// this needs converting to real HTML before it's stored.
		$storeHash['edit'] = ContactWikiIndividual::plainTextToHtmlParagraphs( $storeHash['edit'] );
	}

	if( $gContent->store( $storeHash ) ) {
		if( $wikiQid ) {
			// Re-fetched here (inside reloadFromWikidata()) rather than round-tripped through a
			// hidden form field - the raw entity JSON is ~200KB, which HTML-escaped for an
			// attribute value lands right on post_max_size/client_max_body_size limits (found
			// live: it was silently never arriving at all). The qid alone is tiny and round-trips
			// through the form fine; one extra Wikidata fetch server-side is cheap and far more
			// reliable than carrying that much data through an HTML round-trip at all.
			$gContent->reloadFromWikidata( $wikiQid );
		}
		KernelTools::bit_redirect( CONTACT_PKG_URL.'edit.php?content_id='.$gContent->mContentId );
		die;
	}
}

// Pre-fill from a successful fetch (GET-then-render step) or fall through to whatever was already
// typed (a failed Save re-renders with the same hidden wikidata_qid the form already carried, not
// a fresh fetch) - the raw entity JSON itself is never round-tripped through the form at all (see
// the Save block's own comment), just the qid.
$wikiExternalIds = [];
$wikiSuggestedTypes = [];
$wikiDob = $_REQUEST['dob'] ?? null;
$wikiDod = $_REQUEST['dod'] ?? null;
$wikiImageFilename = $_REQUEST['wikidata_image'] ?? null;
$wikiSitelink = null;

if( $wikiEntity ) {
	$label = $wikiEntity['labels']['en']['value'] ?? '';
	$parts = explode( ' ', trim( $label ) );
	$_REQUEST['surname']  = array_pop( $parts ) ?: '';
	$_REQUEST['forename'] = implode( ' ', $parts );

	foreach( ContactWikiIndividual::EXTERNAL_ID_PROPS as $item => $property ) {
		$value = ContactWikiIndividual::stringClaim( $wikiEntity, $property );
		if( $value !== null ) {
			$wikiExternalIds[$item] = $value;
		}
	}
	foreach( ContactWikiIndividual::occupationQids( $wikiEntity ) as $qid ) {
		if( isset( ContactWikiIndividual::OCCUPATION_MAP[$qid] ) ) {
			// Flag-map, not a list - templates here can't reliably call a bare function like
			// in_array() inside {if} (same Smarty restriction fisheye's own MANUAL.md documents
			// for method_exists()), so membership is checked via plain dot-notation instead.
			$wikiSuggestedTypes[ContactWikiIndividual::OCCUPATION_MAP[$qid]] = true;
		}
	}
	$wikiDob = ContactWikiIndividual::dateClaim( $wikiEntity, 'P569' );
	$wikiDod = ContactWikiIndividual::dateClaim( $wikiEntity, 'P570' );
	$wikiImageFilename = ContactWikiIndividual::imageFilename( $wikiEntity );
	// Wikipedia's own summary, keyed by the enwiki sitelink title already carried on the entity -
	// pre-fills the same Note field every other fetched value pre-fills, since that field IS the
	// Contact's own lc.data ('edit' -> data, not 'data' directly - see LibertyContent::store()'s own
	// convention), not a separate mechanism. Silently does nothing when there's no English Wikipedia
	// article for this entity, or the fetch fails outright.
	$wikiTitle = ContactWikiIndividual::wikipediaTitle( $wikiEntity );
	if( $wikiTitle !== null ) {
		$bio = ContactWikiIndividual::fetchWikipediaSummary( $wikiTitle );
		if( $bio !== null ) {
			$_REQUEST['edit'] = $bio;
		}
	}
	$wikiSitelink = $wikiEntity['sitelinks']['enwiki']['url'] ?? null;
}

$wikiImagePreviewUrl = $wikiImageFilename ? 'https://commons.wikimedia.org/wiki/Special:FilePath/'.rawurlencode( $wikiImageFilename ).'?width=200' : null;

$gBitSmarty->assign( 'gContent', $gContent );
$gBitSmarty->assign( 'errors', $gContent->mErrors );
$gBitSmarty->assign( 'wikiQid', $wikiQid );
$gBitSmarty->assign( 'wikiExternalIds', $wikiExternalIds );
$gBitSmarty->assign( 'wikiSuggestedTypes', $wikiSuggestedTypes );
$gBitSmarty->assign( 'wikiDob', $wikiDob );
$gBitSmarty->assign( 'wikiDod', $wikiDod );
$gBitSmarty->assign( 'wikiImageFilename', $wikiImageFilename );
$gBitSmarty->assign( 'wikiImagePreviewUrl', $wikiImagePreviewUrl );
$gBitSmarty->assign( 'wikiSitelink', $wikiSitelink );
$gBitSmarty->assign( 'personTypes', $gContent->getAvailableTypeItems() );

$gBitSystem->display( 'bitpackage:contact/add_wiki_person.tpl', KernelTools::tra( 'Add Wiki Individual' ), [ 'display_mode' => 'edit' ] );
