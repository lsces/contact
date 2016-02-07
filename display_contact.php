<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_contact/display_contact.php,v 1.7 2010/02/08 21:27:22 wjames5 Exp $
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
require_once( '../kernel/setup_inc.php' );

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_view' );

include_once( CONTACT_PKG_PATH.'lookup_contact_inc.php' );

require_once( FISHEYE_PKG_PATH.'FisheyeGallery.php');
require_once( FISHEYE_PKG_PATH.'FisheyeImage.php');
global $gBitSystem, $fisheyeErrors, $fisheyeWarnings, $fisheyeSuccess;
$lookup = array();
$gGallery = new FisheyeGallery(4);
$gGallery->load();
$gGallery->loadImages(0,4);
$gBitSmarty->assignByRef('gGallery', $gGallery);
$gBitSmarty->assignByRef('galleryId', $gGallery->mGalleryId);
$gBitSmarty->assign('galLayout', 'fixed_grid');

if ( !$gContent->isValid() ) {
	header ("location: ".CONTACT_PKG_URL."list.php");
	die;
}

if( $gContent->isCommentable() ) {
	$commentsParentId = $gContent->mContentId;
	$comments_vars = Array('contact');
	$comments_prefix_var='contact:';
	$comments_object_var='contact';
	$comments_return_url = $_SERVER['PHP_SELF']."?content_id=".$gContent->mContentId;
	include_once( LIBERTY_PKG_PATH.'comments_inc.php' );

 	if ( isset($_REQUEST['post_comment_submit']) and !$_REQUEST['post_comment_submit'] == 'Post' ) {
		header ("location: ".CONTACT_PKG_URL."index.php?content_id=".$gContent->mContentId );
		die;
	}
}

$gContent->mInfo['type'] = $gContent->getContactGroupList();

$gBitSystem->setBrowserTitle("Contact Information");
$gBitSystem->display( 'bitpackage:contact/show_contact.tpl');
?>
