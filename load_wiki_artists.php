<?php
/**
 * Surveys every top-level Music artist/composer gallery (fisheye's own "Music" pool, the same set
 * load_music.php creates galleries for) for whether it already has a linked wiki Contact - via that
 * Contact's own 'music_gallery' xref pointing back at the gallery's content_id. Every gallery is
 * listed either way, same "show the whole picture, not just gaps" spirit as load_music.php's own
 * candidate list - already-linked ones shown for information, not as something to act on.
 *
 * For a gallery with no linked Contact yet: finds a representative registered album's own
 * 'mb_artistid' common-tag xref (see FisheyeAlbum::FISHEYEALBUM_COMMON_TAG_ALTERNATES - only
 * promoted when identical across every track, so a various-artists compilation won't have one),
 * then resolves it via ContactWikiTrait::lookupMusicBrainzArtist() - MusicBrainz's own artist type
 * (Person/Group) decides which class to create, and its own 'wikidata' url-rel supplies the qid
 * without a separate manual Wikidata search (see contact/MANUAL-WIKI.md's own "Finding the Wikidata
 * id without searching for it" section).
 *
 * Nothing is created until this list is reviewed and submitted - a gallery with no mb_artistid at
 * all, or a MusicBrainz artist with no Wikidata link, is shown with no checkbox at all (nothing
 * findable to create from); a clean match is pre-ticked but still just a checkbox, not automatic -
 * unticking is how a wrong/ambiguous match gets blocked before anything is written.
 *
 * @package contact
 * @subpackage functions
 */

namespace Bitweaver\Contact;

use Bitweaver\Fisheye\FisheyeAlbum;
use Bitweaver\Fisheye\FisheyeGallery;
use Bitweaver\KernelTools;

require_once '../kernel/includes/setup_inc.php';

global $gBitSystem, $gBitSmarty, $gBitDb;

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_update' );

const LOAD_WIKI_ARTISTS_LIMIT = 60;

// Does any Contact already have a 'music_gallery' xref pointing at this gallery's own content_id -
// the reverse of the lookup Contact::load() itself does (content_id -> its own music_gallery), here
// content_id <- whichever Contact's music_gallery xref names it. No generic helper for this
// direction exists yet (every LibertyContent:: lookup helper goes content_id -> its own xrefs, not
// "who points at me"), so a small direct read - fine per this stack's own read-only convention.
function load_wiki_artists_existing_contact( int $pGalleryContentId ): ?array {
	global $gBitDb;
	return $gBitDb->getRow(
		"SELECT x.content_id, lc.title FROM `".BIT_DB_PREFIX."liberty_xref` x
		 JOIN `".BIT_DB_PREFIX."liberty_content` lc ON lc.content_id = x.content_id
		 WHERE x.item = 'music_gallery' AND x.xref = ?",
		[ $pGalleryContentId ]
	) ?: null;
}

// The first (lowest xorder) 'mb_artistid' found on any registered album directly under this
// gallery - a various-artists compilation's own albums won't have one at all (the tag only gets
// promoted when it's identical across every track), which is exactly the "nothing to resolve from"
// case this is meant to surface. mb_artistid is now multiple=1 (FisheyeAlbum::storeCommonTagXref()
// splits a bundled multi-id tag value into one row per id, xorder preserving the original list
// order) - explicit ORDER BY xorder here, not LibertyContent::lookupXrefByItem()'s own unordered
// "FIRST 1", since the lowest xorder is specifically the one confirmed (live, Samuel Barber's own
// gallery) to be the same person across every album under one composer's gallery - the actual
// composer/primary artist credit, not just an arbitrary row.
function load_wiki_artists_mb_artist_id( FisheyeGallery $pGallery ): ?string {
	global $gBitDb;
	// loadImages() takes its param by reference - a literal array can't bind to that, needs a real
	// variable first (same gotcha already hit and fixed for storeXref() elsewhere in this package).
	$listHash = [ 'max_records' => 20 ];
	$pGallery->loadImages( $listHash );
	foreach( $pGallery->mItems as $item ) {
		if( !( $item instanceof FisheyeAlbum ) ) {
			continue;
		}
		$value = $gBitDb->getOne(
			"SELECT FIRST 1 x.xkey_ext FROM `".BIT_DB_PREFIX."liberty_xref` x
			 WHERE x.content_id = ? AND x.item = 'mb_artistid' AND ( x.end_date IS NULL OR x.end_date > ? )
			 ORDER BY x.xorder",
			[ $item->mContentId, time() ]
		);
		if( !empty( $value ) ) {
			return $value;
		}
	}
	return null;
}

$topGalleryId = FisheyeGallery::getTopGalleryId( 'Music' );
$topGallery = $topGalleryId ? new FisheyeGallery( $topGalleryId ) : null;
if( $topGallery ) {
	$topGallery->load();
}

$result = null;
if( !empty( $_REQUEST['fCreate'] ) ) {
	$result = [ 'created' => [], 'errors' => [] ];
	foreach( (array)( $_REQUEST['selected'] ?? [] ) as $galleryContentId ) {
		$galleryContentId = (int)$galleryContentId;
		$wikiQid = trim( (string)( $_REQUEST['qid_'.$galleryContentId] ?? '' ) );
		$mbType = trim( (string)( $_REQUEST['mbtype_'.$galleryContentId] ?? '' ) );
		if( !$galleryContentId || !$wikiQid || load_wiki_artists_existing_contact( $galleryContentId ) ) {
			continue;
		}
		$entity = ContactWikiIndividual::fetchWikidataEntity( $wikiQid );
		if( !$entity ) {
			$result['errors'][] = [ 'gallery_content_id' => $galleryContentId, 'error' => KernelTools::tra( 'Could not fetch that Wikidata entity.' ) ];
			continue;
		}
		$label = trim( $entity['labels']['en']['value'] ?? '' );
		$isGroup = strcasecmp( $mbType, 'Person' ) !== 0;

		if( $isGroup ) {
			$gContent = new ContactWikiGroup();
			$contactTypes = [];
			foreach( ContactWikiGroup::instanceOfQids( $entity ) as $qid ) {
				if( isset( ContactWikiGroup::GROUP_TYPE_MAP[$qid] ) ) {
					$contactTypes[] = ContactWikiGroup::GROUP_TYPE_MAP[$qid];
				}
			}
			$storeHash = [ 'organisation' => $label, 'fContactTypesSubmitted' => 1, 'contact_types' => $contactTypes ];
		} else {
			$gContent = new ContactWikiIndividual();
			$parts = explode( ' ', $label );
			$surname = array_pop( $parts ) ?: '';
			$forename = implode( ' ', $parts );
			$contactTypes = [];
			foreach( ContactWikiIndividual::occupationQids( $entity ) as $qid ) {
				if( isset( ContactWikiIndividual::OCCUPATION_MAP[$qid] ) ) {
					$contactTypes[] = ContactWikiIndividual::OCCUPATION_MAP[$qid];
				}
			}
			$storeHash = [ 'forename' => $forename, 'surname' => $surname, 'fContactTypesSubmitted' => 1, 'contact_types' => $contactTypes ];
		}

		if( $gContent->store( $storeHash ) ) {
			$gContent->reloadFromWikidata( $wikiQid );
			$gContent->upsertXref( $gContent->mContentId, 'music_gallery', [ 'xref' => $galleryContentId ] );
			$result['created'][] = [ 'gallery_content_id' => $galleryContentId, 'title' => $label, 'content_id' => $gContent->mContentId ];
		} else {
			$result['errors'][] = [ 'gallery_content_id' => $galleryContentId, 'error' => implode( '; ', $gContent->mErrors ) ];
		}
	}
}

$candidates = [];
if( $topGallery && $topGallery->isValid() ) {
	$topGalleryListHash = [ 'max_records' => LOAD_WIKI_ARTISTS_LIMIT ];
	$topGallery->loadImages( $topGalleryListHash );
	foreach( $topGallery->mItems as $artistGallery ) {
		if( !( $artistGallery instanceof FisheyeGallery ) ) {
			continue;
		}
		$candidate = [
			'gallery_content_id' => $artistGallery->mContentId,
			'title'              => $artistGallery->mInfo['title'] ?? '',
			'existing_contact'   => load_wiki_artists_existing_contact( $artistGallery->mContentId ),
			'mb_match'           => null,
			'wikidata_qid'       => null,
			'note'               => null,
		];
		if( !$candidate['existing_contact'] ) {
			$mbArtistId = load_wiki_artists_mb_artist_id( $artistGallery );
			if( !$mbArtistId ) {
				$candidate['note'] = KernelTools::tra( 'No MusicBrainz artist id found on any registered album.' );
			} else {
				// MusicBrainz's own etiquette asks for roughly one request/second unauthenticated -
				// this loop is the first genuinely batched external caller in this package, the rest
				// are all one-off, user-triggered single lookups.
				usleep( 1100000 );
				$mbMatch = ContactWikiGroup::lookupMusicBrainzArtist( $mbArtistId );
				if( !$mbMatch ) {
					$candidate['note'] = KernelTools::tra( 'MusicBrainz artist id no longer resolves.' );
				} elseif( !$mbMatch['wikidata_qid'] ) {
					$candidate['note'] = KernelTools::tra( 'That MusicBrainz artist has no linked Wikidata id.' );
				} else {
					$candidate['mb_match'] = $mbMatch;
					$candidate['wikidata_qid'] = $mbMatch['wikidata_qid'];
				}
			}
		}
		$candidates[] = $candidate;
	}
}

$gBitSmarty->assign( 'candidates', $candidates );
$gBitSmarty->assign( 'candidateLimit', LOAD_WIKI_ARTISTS_LIMIT );
$gBitSmarty->assign( 'result', $result );

$gBitSystem->display( 'bitpackage:contact/load_wiki_artists.tpl', KernelTools::tra( 'Load Wiki Artist Contacts' ), [ 'display_mode' => 'edit' ] );
