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

/**
 * $data[0] - Name
 * $data[1] - Address House
 * $data[2] - POSTCODE
 * $data[3] - Property Phone
 */
		$pDataHash['title'] = $data[0];
		$pDataHash['address_store']['house'] = $data[1];
		$pDataHash['address_store']['postcode'] = $data[2];
		$pDataHash['property_store']['telephone'] = $data[3];

		$this->mDb->StartTrans();
		$this->mContentId = 0;
		$pDataHash['content_id'] = 0;
		if ( LibertyContent::store( $pDataHash ) ) {
			$pDataHash['contact_store']['content_id'] = $pDataHash['content_id'];
			
			$result = $this->mDb->associateInsert( $table, $pDataHash['contact_store'] );
			$this->mDb->CompleteTrans();				
		} else {
			$this->mDb->RollbackTrans();
			$this->mErrors['store'] = 'Failed to store this contact.';
		}				
		$Xref = new ContactXref();
		$pParams = array( 'source' => '#S', 
						'content_id' => $pDataHash['content_id'], 
						'xkey' => $data[2], 
						'xorder' => '0', 
						'xkey_ext' => $data[1] );
		$Xref->store($pParams);
		$pParams = array( 'source' => '#P', 
						'content_id' => $pDataHash['content_id'], 
						'xkey' => '', 
						'xorder' => '0', 
						'xkey_ext' => 'External patrol of property. No keys. See site list in photos. Phone Ideal Alarms once completed.' );
		$Xref->store($pParams);
		$pParams = array( 'source' => '#P', 
						'content_id' => $pDataHash['content_id'], 
						'xkey' => '08007 076595', 
						'xorder' => '1', 
						'xkey_ext' => NULL );
		$Xref->store($pParams);
		$pParams = array( 'source' => '#P', 
						'content_id' => $pDataHash['content_id'], 
						'xkey' => $data[1], 
						'xorder' => '2', 
						'xkey_ext' => 'Property number' );
		$Xref->store($pParams);

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
