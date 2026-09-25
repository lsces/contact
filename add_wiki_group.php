<?php
/**
 * Add a ContactWikiGroup seeded from a Wikidata entity - organisation name, WBxx type-tag
 * suggestions (from ContactWikiGroup::GROUP_TYPE_MAP, a curated lookup, not a raw mirror of
 * Wikidata's own broader P31 "instance of" list), the external-identity links, formed/disbanded,
 * and a downloaded P18 image - same two-step fetch-then-save flow and shared
 * ContactWikiTrait::reloadFromWikidata() cascade as add_wiki_person.php, adapted for a group's own
 * plain organisation-name storage (no forename/surname/NAME xref) instead of a person's. See
 * contact/MANUAL-WIKI.md.
 *
 * @package contact
 * @subpackage functions
 */

use Bitweaver\Contact\ContactWikiGroup;
use Bitweaver\KernelTools;

require_once '../kernel/includes/setup_inc.php';

global $gBitSystem, $gBitSmarty, $gBitUser;

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_update' );

$gContent = new ContactWikiGroup();
$wikiEntity = null;
$wikiQid = null;

if( !empty( $_REQUEST['fCancel'] ) ) {
	KernelTools::bit_redirect( CONTACT_PKG_URL );
	die;
}

if( !empty( $_REQUEST['fFetchWikidata'] ) ) {
	$rawInput = trim( (string)( $_REQUEST['wikidata_input'] ?? '' ) );
	$wikiQid = ContactWikiGroup::extractQid( $rawInput );
	if( !$wikiQid ) {
		// Not a Wikidata id/URL - try it as a MusicBrainz artist id/URL instead, resolving via that
		// artist's own 'wikidata' url-rel (confirmed live against Fleetwood Mac's own MusicBrainz
		// artist entity - see resolveWikidataQidFromMusicBrainzArtist()'s own docblock) rather than
		// making the user go and search Wikidata separately. This is the common case for a group -
		// its MusicBrainz artist id is usually already known from the album tags fisheye scanned in.
		$mbArtistId = ContactWikiGroup::extractMusicBrainzArtistId( $rawInput );
		if( $mbArtistId ) {
			$wikiQid = ContactWikiGroup::resolveWikidataQidFromMusicBrainzArtist( $mbArtistId );
			if( !$wikiQid ) {
				$gContent->mErrors[] = KernelTools::tra( 'That MusicBrainz artist has no linked Wikidata id.' );
			}
		} else {
			$gContent->mErrors[] = KernelTools::tra( 'Not a recognisable Wikidata id/URL or MusicBrainz artist id/URL.' );
		}
	}
	if( $wikiQid ) {
		$wikiEntity = ContactWikiGroup::fetchWikidataEntity( $wikiQid );
		if( !$wikiEntity ) {
			$gContent->mErrors[] = KernelTools::tra( 'Could not fetch that Wikidata entity.' );
			$wikiQid = null;
		}
	}
}

if( !empty( $_REQUEST['fSaveContact'] ) ) {
	$_REQUEST['contact_types'] = array_values( (array)( $_REQUEST['contact_types'] ?? [] ) );
	$wikiQid = trim( (string)( $_REQUEST['wikidata_qid'] ?? '' ) ) ?: null;

	// Same reasoning as add_wiki_person.php's own copy - a separate copy for store(), not a
	// mutation of $_REQUEST['edit'] itself, so a failed store() re-renders the plain, readable text.
	$storeHash = $_REQUEST;
	if( !empty( $storeHash['edit'] ) && strip_tags( $storeHash['edit'] ) === $storeHash['edit'] ) {
		$storeHash['edit'] = ContactWikiGroup::plainTextToHtmlParagraphs( $storeHash['edit'] );
	}

	if( $gContent->store( $storeHash ) ) {
		if( $wikiQid ) {
			$gContent->reloadFromWikidata( $wikiQid );
		}
		KernelTools::bit_redirect( CONTACT_PKG_URL.'edit.php?content_id='.$gContent->mContentId );
		die;
	}
}

$wikiExternalIds = [];
$wikiSuggestedTypes = [];
$wikiFormed = $_REQUEST['formed'] ?? null;
$wikiDisbanded = $_REQUEST['disbanded'] ?? null;
$wikiImageFilename = $_REQUEST['wikidata_image'] ?? null;
$wikiSitelink = null;

if( $wikiEntity ) {
	$_REQUEST['organisation'] = trim( $wikiEntity['labels']['en']['value'] ?? '' );

	foreach( ContactWikiGroup::EXTERNAL_ID_PROPS as $item => $property ) {
		$value = ContactWikiGroup::stringClaim( $wikiEntity, $property );
		if( $value !== null ) {
			$wikiExternalIds[$item] = $value;
		}
	}
	foreach( ContactWikiGroup::instanceOfQids( $wikiEntity ) as $qid ) {
		if( isset( ContactWikiGroup::GROUP_TYPE_MAP[$qid] ) ) {
			$wikiSuggestedTypes[ContactWikiGroup::GROUP_TYPE_MAP[$qid]] = true;
		}
	}
	$wikiFormed = ContactWikiGroup::dateClaim( $wikiEntity, 'P571' );
	$wikiDisbanded = ContactWikiGroup::dateClaim( $wikiEntity, 'P576' );
	$wikiImageFilename = ContactWikiGroup::imageFilename( $wikiEntity );
	// Wikipedia's own summary, keyed by the enwiki sitelink title - see add_wiki_person.php's own
	// identical block for the full reasoning; unlike TMDb (never used here at all - it has no
	// concept of a "band" biography), this works the same for a group's own article.
	$wikiTitle = ContactWikiGroup::wikipediaTitle( $wikiEntity );
	if( $wikiTitle !== null ) {
		$bio = ContactWikiGroup::fetchWikipediaSummary( $wikiTitle );
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
$gBitSmarty->assign( 'wikiFormed', $wikiFormed );
$gBitSmarty->assign( 'wikiDisbanded', $wikiDisbanded );
$gBitSmarty->assign( 'wikiImageFilename', $wikiImageFilename );
$gBitSmarty->assign( 'wikiImagePreviewUrl', $wikiImagePreviewUrl );
$gBitSmarty->assign( 'wikiSitelink', $wikiSitelink );
$gBitSmarty->assign( 'groupTypes', $gContent->getAvailableTypeItems() );

$gBitSystem->display( 'bitpackage:contact/add_wiki_group.tpl', KernelTools::tra( 'Add Wiki Group' ), [ 'display_mode' => 'edit' ] );
