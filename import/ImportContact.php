<?php
/**
 * @version $Header:$
 *
 * Copyright ( c ) 2006 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * @package contact
 */

	/**
	 * ContactRecordLoad( $data ); 
	 * Uncomment to enable
	 */
	function ContactRecordLoad( &$data ) {
		$table = BIT_DB_PREFIX."contact";
//		$atable = BIT_DB_PREFIX."contact_address";

		$usn = 10000 + $data[0];
		$pDataHash['contact_store']['content_id'] = $data[0];
		$pDataHash['address_store']['content_id'] = $data[0];
		$pDataHash['contact_store']['contact_id'] = $usn;
		$pDataHash['address_store']['usn'] = $usn;
		$pDataHash['address_store']['organisation'] = $data[1];
		if ( $data[2] == 'D' ) $type = 0; else $type = 1;
		$pDataHash['address_store']['sao'] = '';
		$pDataHash['address_store']['pao'] = '';
		$pDataHash['address_store']['number'] = '';
		$pDataHash['address_store']['street'] = $data[4];
		$pDataHash['address_store']['locality'] = $data[5];
		$pDataHash['address_store']['town'] = $data[6];
		$pDataHash['address_store']['county'] = $data[7];
		$pDataHash['contact_store']['postcode'] = $data[8];
		$pDataHash['address_store']['postcode'] = $data[8];

		$this->mDb->StartTrans();
		$this->mContentId = 0;
		$pDataHash['content_id'] = 0;
		if ( LibertyContent::store( $pDataHash ) ) {
			$pDataHash['contact_store']['content_id'] = $pDataHash['content_id'];
			$pDataHash['address_store']['content_id'] = $pDataHash['content_id'];
			
			$result = $this->mDb->associateInsert( $table, $pDataHash['contact_store'] );
//			$result = $this->mDb->associateInsert( $atable, $pDataHash['address_store'] );
			$this->mDb->CompleteTrans();				
		} else {
			$this->mDb->RollbackTrans();
			$this->mErrors['store'] = 'Failed to store this contact.';
		}				
		return( count( $this->mErrors ) == 0 ); 
	}
	
	/**
	 * Delete contact object and all related records
	 */
	function ContactDataExpunge()
	{
		$ret = FALSE;
		$query = "DELETE FROM `".BIT_DB_PREFIX."contact`";
		$result = $this->mDb->query( $query );
//		$query = "DELETE FROM `".BIT_DB_PREFIX."contact_address`";
//		$result = $this->mDb->query( $query );
		$query = "DELETE FROM `".BIT_DB_PREFIX."contact_xref`";
		$result = $this->mDb->query( $query );
		return $ret;
	}
?>
