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

$gBitSystem->verifyPackage( 'contact' );
$gBitSystem->verifyPermission( 'p_contact_update' );

include_once( CONTACT_PKG_PATH.'lookup_contact_inc.php' );

if( !empty( $gContent->mInfo ) ) {
	$formInfo = $gContent->mInfo;
	$formInfo['edit'] = !empty( $gContent->mInfo['data'] ) ? $gContent->mInfo['data'] : '';
}

require_once 'import/Mbox.php';
require_once 'import/mimeDecode.php';

//reads a mbox file
$file = '/srv/website/bitweaver/contact/data/Stockport';
echo 'Using file ' . $file . "<br>";

$mbox = new Mail_Mbox($file);
$mbox->open();

for ($n = 0; $n < $mbox->size(); $n++) {
    $message = $mbox->get($n);

    preg_match('/Subject: (.*)$/m', $message, $matches);
    $subject = $matches[1];
    echo 'Mail #' . $n . ': ' . $subject . "<br>";
    $Decoder = new Mail_mimeDecode( $message );
    $params = array(
    'include_bodies' => TRUE,
    'decode_bodies'  => TRUE,
    'decode_headers' => TRUE
	);
	$Decoded = $Decoder->decode($params);   
	if ( $Decoded->ctype_primary == "multipart" ) {
		vd($Decoded->parts[0]->ctype_primary);	
		vd($Decoded->parts[0]->ctype_secondary);	
		print($Decoded->parts[0]->body);
	} else {
		vd($Decoded->ctype_primary);	
		vd($Decoded->ctype_secondary);	
		print($Decoded->body);
	}
	vd($Decoded->headers);
	print( $message );
}

$mbox->close();

// formInfo might be set due to a error on submit
if( empty( $formInfo ) ) {
	$formInfo = &$gContent->mInfo;
}

$formInfo['contact_type_list'] = $gContent->getContactSourceList();
$gBitSmarty->assignByRef( 'pageInfo', $formInfo );

$gBitSmarty->assignByRef( 'errors', $gContent->mErrors );
$gBitSmarty->assign( (!empty( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : 'body').'TabSelect', 'tdefault' );
$gBitSmarty->assign('show_page_bar', 'y');

$gBitSystem->display( 'bitpackage:contact/edit.tpl', 'Edit: ' , array( 'display_mode' => 'edit' ));
?>
