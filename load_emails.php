<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_contact/edit.php,v 1.6 2010/02/08 21:27:22 wjames5 Exp $
 *
 * Copyright (c) 2006 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * @package contact
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../kernel/setup_inc.php' );
require_once( LIBERTY_PKG_PATH.'LibertyComment.php' );

global $commentsLib, $gBitSmarty, $gBitSystem;

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_update' );

include_once( CONTACT_PKG_PATH.'lookup_contact_inc.php' );

if ( !$gContent->mContentId ) {
	header ("location: ".CONTACT_PKG_URL."list.php");
	die;
}

require_once 'import/Mbox.php';
require_once 'import/mimeDecode.php';

//reads a mbox file
$file = '/srv/website/bitweaver/contact/data/Shipped';
//echo 'Using file ' . $file . "<br>";

$mbox = new Mail_Mbox($file);
$mbox->open();

for ($n = 0; $n < $mbox->size(); $n++) {
    $message = $mbox->get($n);

    preg_match('/Subject: (.*)$/m', $message, $matches);
	if ( isset( $matches[1] ) ) {
    	$subject = $matches[1];
	} else {
		$subject = 'Not Set';
	}
//    echo 'Mail #' . $n . ': ' . $subject . "<br>";
    $Decoder = new Mail_mimeDecode( $message );
    $params = array(
    'include_bodies' => TRUE,
    'decode_bodies'  => TRUE,
    'decode_headers' => TRUE
	);
	$Decoded = $Decoder->decode($params);   
	if ( strtolower($Decoded->ctype_primary) == "multipart" ) {
//		vd($Decoded->parts[0]->ctype_primary);	
		$ctype_secondary = $Decoded->parts[0]->ctype_secondary;	
		if ( strtolower($Decoded->parts[0]->ctype_primary) == "multipart" ) {
			if ( !empty($Decoded->parts[0]->parts[0]->body) ) {
				$body = $Decoded->parts[0]->parts[0]->body;
			} else {
				vd( $Decoded );
			}
		} else {
			$body = $Decoded->parts[0]->body;
		}
	} else if ( strtolower($Decoded->ctype_primary) == "text" ) {
//		vd($Decoded->ctype_primary);	
		$ctype_secondary = $Decoded->ctype_secondary;	
		if ( !empty($Decoded->body) ) {
			$body = $Decoded->body;
		} else {
			vd( $Decoded );
		}
	} else {
		vd( $message );
		vd($Decoded);
		break;
	}
	$em_date = str_replace( '(GMT Daylight Time)', 'BST', $Decoded->headers['date']  );
	$em_date = str_replace( '(West-Europa (standaardtijd))', '', $em_date  );
	$dateTime = new DateTime( $em_date );

	$email = Array();
	$email['comments_parent_id'] = $gContent->mContentId;
	$email['comment_title'] = $subject;
	$email['comment_data'] = $body;
	$email['created'] = strtotime( $dateTime->format(DATE_ATOM) );
	$email['last_modified'] = strtotime( $dateTime->format(DATE_ATOM) );
	$email['summary'] = $message;
	if ( $ctype_secondary == 'html' ) {
		$email['format_guid'] = 'bithtml';
	} else {
		$email['format_guid'] = 'simpletext';
	}

	$storeComment = new LibertyComment();
/*
 * Process email addresses from the file
	// define our search pattern to extract any email addresses
//	$pattern = '#(?<=s/)[^\s@]+@[^\s@_]#';
//	$pattern = '/([A-Za-z0-9\.\-\_\!\#\$\%\&\'\*\+\/\=\?\^\`\{\|\}]+)\@([A-Za-z0-9.-_]+)(\.[A-Za-z]{2,5})/';
	$pattern = '/([a-z0-9_\.\-])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,4})+/i';
	// preg match all in the string
	preg_match_all( $pattern, $Decoded->headers['from'], $emails );
 	if ( !empty($emails[0]) ) {
		if ( $emails[0][0] == 'lester@lsces.co.uk' ) {
			preg_match_all( $pattern, $Decoded->headers['to'], $to_emails );
		}

		if ( !empty($to_emails[0]) ) {
			$from_emails = array_unique($to_emails[0]);
		} else {
			$from_emails = array_unique($emails[0]);
		}

		$sql = "SELECT e.* FROM `".BIT_DB_PREFIX."contact_email` e WHERE e.`email` = ?";
		$result = $storeComment->mDb->getRow( $sql, $from_emails );
		if( $result ) {
			$table = BIT_DB_PREFIX."contact_email";
			$email['email_store']['end_date'] = strtotime( $dateTime->format(DATE_ATOM) );

			$result = $storeComment->mDb->associateUpdate( $table, $email['email_store'], array( "email" => $from_emails[0] ) );	
		} else {
			$table = BIT_DB_PREFIX."contact_email";
			$email['email_store']['email'] = $from_emails[0];
			$email['email_store']['start_date'] = strtotime( $dateTime->format(DATE_ATOM) );
			$email['email_store']['end_date'] = strtotime( $dateTime->format(DATE_ATOM) );
			
			$result = $storeComment->mDb->associateInsert( $table, $email['email_store'] );			
		}
	} else {
		vd( $Decoded->headers );
	}
 */
	// define our search pattern to extract any email addresses
//	$pattern = '#(?<=s/)[^\s@]+@[^\s@_]#';
//	$pattern = '/([A-Za-z0-9\.\-\_\!\#\$\%\&\'\*\+\/\=\?\^\`\{\|\}]+)\@([A-Za-z0-9.-_]+)(\.[A-Za-z]{2,5})/';
	$pattern = '/([a-z0-9_\.\-])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,4})+/i';
	// preg match all in the string
	preg_match_all( $pattern, $Decoded->headers['from'], $emails );
 	if ( !empty($emails[0]) ) {
		if ( $emails[0][0] == 'lester@lsces.co.uk' ) {
			preg_match_all( $pattern, $Decoded->headers['to'], $to_emails );
		}

		if ( !empty($to_emails[0]) ) {
			$from_emails = array_unique($to_emails[0]);
		} else {
			$from_emails = array_unique($emails[0]);
		}

		$sql = "SELECT e.content_id FROM `".BIT_DB_PREFIX."contact_xref` e WHERE e.`xkey_ext` = ?";
		$result = $storeComment->mDb->getRow( $sql, $from_emails );

		if( $result ) {
			$email['comments_parent_id'] = $result['content_id'];

		} else {
			$sql = "SELECT e.* FROM `".BIT_DB_PREFIX."contact_email` e WHERE e.`email` = ?";
			$result = $storeComment->mDb->getRow( $sql, $from_emails );
			if( $result ) {
				$table = BIT_DB_PREFIX."contact_email";
				$email['email_store']['end_date'] = strtotime( $dateTime->format(DATE_ATOM) );
	
				$result = $storeComment->mDb->associateUpdate( $table, $email['email_store'], array( "email" => $from_emails[0] ) );
			} else {	
				$table = BIT_DB_PREFIX."contact_email";
				$email['email_store']['email'] = $from_emails[0];
				$email['email_store']['start_date'] = strtotime( $dateTime->format(DATE_ATOM) );
				$email['email_store']['end_date'] = strtotime( $dateTime->format(DATE_ATOM) );

				$result = $storeComment->mDb->associateInsert( $table, $email['email_store'] );
			}			
		}
	} else {
		vd( $Decoded->headers );
	}
/*		
	if( $storeComment->storeComment( &$email )) {
		// store successful
	}
*/
}

$mbox->close();

if( $gContent->isCommentable() ) {
	$commentsParentId = $gContent->mContentId;
	$comments_vars = Array('contact');
	$comments_prefix_var='contact:';
	$comments_object_var='contact';
	$comments_return_url = $_SERVER['PHP_SELF']."?content_id=".$gContent->mContentId;
	include_once( LIBERTY_PKG_PATH.'comments_inc.php' );
}

$gContent->mInfo['type'] = $gContent->getContactGroupList();

$gBitSystem->setBrowserTitle("Contact Information");
$gBitSystem->display( 'bitpackage:contact/show_contact.tpl');
?>
