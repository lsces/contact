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

$gBitSystem->setBrowserTitle( $gContent->getTitle() );
$gBitSystem->display( 'bitpackage:contact/show_contact.tpl');
