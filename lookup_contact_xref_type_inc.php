<?php
/**
 * @version $Header$
 * @package contact
 * @subpackage functions
 */

/**
 * Initialization
 */
	global $gContent;
	require_once( CONTACT_PKG_PATH.'Contact.php');
	
	// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
	if( empty( $gContent ) || !is_object( $gContent ) ) {
		if (!empty($_REQUEST['xref_id']) && is_numeric($_REQUEST['xref_id'])) {
			$gContent = new ContactXref( $_REQUEST['xref_id'] );
			$gContent = new ContactXref( $_REQUEST['xref_id'] );
		} else {
			$gContent = new Contact();
		}

		$gBitSmarty->assignByRef( 'gContent', $gContent );
	}
?>
