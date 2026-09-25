<?php
/**
 * Renamed from display_contact.php 2026-08-10 — no separate person/business display, so no
 * suffix needed, matching the bare view.php/edit.php pattern used elsewhere in the codebase.
 *
 * Copyright (c) 2006 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 *
 * @package contact
 * @subpackage functions
 */

/**
 * required setup
 */
use Bitweaver\Contact\ContactWikiIndividual;
use Bitweaver\Fisheye\FisheyeGallery;

require_once '../kernel/includes/setup_inc.php';

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_view' );

include_once CONTACT_PKG_INCLUDE_PATH . 'lookup_contact_inc.php';

global $gBitSystem, $fisheyeErrors, $fisheyeWarnings, $fisheyeSuccess;
$lookup = [];
$lookup['max_records'] = 4;
$gGallery = new FisheyeGallery( $gContent->mInfo['client_gallery'] );
$gGallery->load();
$gGallery->loadImages( $lookup );
$gBitSmarty->assign( 'gGallery', $gGallery );
$gBitSmarty->assign( 'galleryId', $gGallery->mGalleryId );
$gBitSmarty->assign( 'galLayout', 'fixed_grid' );

if (!$gContent->isValid()) {
	header( "location: " . CONTACT_PKG_URL . "list_contacts.php" );
	die;
}

if ($gContent->isCommentable()) {
	$commentsParentId = $gContent->mContentId;
	$comments_vars = [ 'contact' ];
	$comments_prefix_var = 'contact:';
	$comments_object_var = 'contact';
	$comments_return_url = $_SERVER['PHP_SELF'] . "?content_id=" . $gContent->mContentId;
	include_once LIBERTY_PKG_INCLUDE_PATH . 'comments_inc.php';

	if (isset($_REQUEST['post_comment_submit']) and !$_REQUEST['post_comment_submit'] == 'Post' ) {
		header ("location: ".CONTACT_PKG_URL."index.php?content_id=".$gContent->mContentId );
		die;
	}
}

$gBitSmarty->assign( 'isPerson', $gContent instanceof \Bitweaver\Contact\ContactPerson );
$gBitSmarty->assign( 'gXrefInfo', $gContent->mXrefInfo );

$isWikiIndividual = $gContent instanceof ContactWikiIndividual;
$gBitSmarty->assign( 'isWikiIndividual', $isWikiIndividual );

if( $isWikiIndividual ) {
	// Role-flag pills (WPxx currently ticked) - getAvailableTypeItems() is the full code=>name
	// lookup, getSetTypeItems() (Contact.php) is which of those are actually set here; type
	// markers are excluded from mXrefInfo by design (see LibertyXrefType's own docblock), so this
	// needs both rather than reading mXrefInfo the way everything else on this page does.
	$typeNames = [];
	foreach( $gContent->getAvailableTypeItems() as $t ) {
		$typeNames[$t['item']] = $t['name'];
	}
	$roleFlags = [];
	foreach( $gContent->getSetTypeItems() as $item ) {
		if( isset( $typeNames[$item] ) ) {
			$roleFlags[] = $typeNames[$item];
		}
	}
	$gBitSmarty->assign( 'roleFlags', $roleFlags );

	// External identity links - same xkey-falling-back-to-xkey_ext href-building as liberty's own
	// view_href_item.tpl (a MusicBrainz/URL-shaped value lives in xkey_ext, everything else in xkey).
	$externalLinks = [];
	foreach( $gContent->mXrefInfo->mGroups as $xrefGroup ) {
		if( $xrefGroup->mXGroup !== 'external' ) {
			continue;
		}
		foreach( $xrefGroup->mXrefs as $xrefInfo ) {
			$key = $xrefInfo['xkey'] ?: $xrefInfo['xkey_ext'];
			if( $xrefInfo['cross_ref_href'] && $key ) {
				$externalLinks[] = [ 'title' => $xrefInfo['xref_title'], 'url' => $xrefInfo['cross_ref_href'].$key ];
			}
		}
	}
	$gBitSmarty->assign( 'externalLinks', $externalLinks );

	// Large profile thumbnail - the first downloaded Wikidata image, if any (item is multiple=1,
	// but a wiki contact only ever has the one auto-downloaded image today).
	if( $imageXref = $gContent->mXrefInfo->findRowByItem( 'image' ) ) {
		$gBitSmarty->assign( 'wikiThumbnailUrl', CONTACT_PKG_URL.'view_extra_image.php?xref_id='.$imageXref['xref_id'] );
	}

	// dob/dod are xref items, not mInfo columns Contact::load() populates for every contact (unlike
	// client_gallery/x_coordinate etc.) - wiki-specific, so read here rather than in Contact.php.
	if( $dobXref = $gContent->mXrefInfo->findRowByItem( 'dob' ) ) {
		$gBitSmarty->assign( 'wikiDob', $dobXref['xkey_ext'] );
	}
	if( $dodXref = $gContent->mXrefInfo->findRowByItem( 'dod' ) ) {
		$gBitSmarty->assign( 'wikiDod', $dodXref['xkey_ext'] );
	}

	// "Other content" - the linked FisheyeGallery (this contact's own discography), if any. Manually
	// linked via the 'music_gallery' xref item for now (add_xref.php's own 'gallery' template) -
	// see contact.php's own site-local scheme comment for why this isn't automatic yet.
	if( !empty( $gContent->mInfo['music_gallery'] ) ) {
		$gMusicGallery = new FisheyeGallery( $gContent->mInfo['music_gallery'] );
		$gMusicGallery->load();
		$gMusicGallery->loadImages( [ 'max_records' => 24 ] );
		$gBitSmarty->assign( 'gMusicGallery', $gMusicGallery );
	}
}

$gBitSystem->setBrowserTitle( $gContent->getTitle() );
$gBitSystem->display( $isWikiIndividual ? 'bitpackage:contact/view_wiki_profile.tpl' : 'bitpackage:contact/show_contact.tpl' );
