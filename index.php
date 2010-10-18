<?php
/**
 * $Header:$
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

if ( !$gContent->mContentId ) {
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
}

	$gContent->mInfo['type'] = $gContent->getContactGroupList();
	$gBitSystem->setBrowserTitle("Contact List Item");
	$gBitSystem->display( 'bitpackage:contact/show_contact.tpl', NULL, array( 'display_mode' => 'display' ));
?>
