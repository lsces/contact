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
	 * wandeRecordLoad( $data ); 
	 * wande data file import 
	 */
	function wandeRecordLoad( &$data, $cnt ) {
		$ctable = BIT_DB_PREFIX."contact";
		$wtable = BIT_DB_PREFIX."contact_wande";
		$atable = BIT_DB_PREFIX."contact_address";

		$pDataHash['contact_store']['parent_id'] = 1;
		$pDataHash['contact_store']['xkey'] = $data[0];
		$pDataHash['title'] = $data[6];
		if ( strlen($data[5]) > 0 ) {
			$pDataHash['title'] = $data[5];
			if ( strlen($data[4]) > 0 ) $pDataHash['title'] .= ', '.$data[4];
		}

		$pDataHash['wande_store']['content_id'] = $cnt;
		$pDataHash['address_store']['content_id'] = $cnt;
		$pDataHash['wande_store']['contract'] = $data[0];
		$pDataHash['wande_store']['username'] = $data[1];
		$pDataHash['wande_store']['passwd'] = $data[2];
		$pDataHash['wande_store']['djidnumber'] = $data[3];
		$pDataHash['wande_store']['forename'] = $data[4];
		$pDataHash['wande_store']['surname'] = $data[5];
		$pDataHash['wande_store']['organisation'] = $data[6];
		$pDataHash['wande_store']['home_phone'] = $data[7];
		$pDataHash['wande_store']['work_phone'] = $data[8];
		$pDataHash['wande_store']['mobile_phone'] = $data[9];
		$pDataHash['wande_store']['fax'] = $data[10];
		$pDataHash['wande_store']['email'] = $data[11];
		$pDataHash['wande_store']['website'] = $data[12];
		$pDataHash['address_store']['sao'] = '';
		$pDataHash['address_store']['pao'] = '';
		$pDataHash['address_store']['number'] = '';
		$pDataHash['address_store']['street'] = $data[13];
		$pDataHash['address_store']['locality'] = $data[14];
		$pDataHash['address_store']['town'] = $data[15];
		$pDataHash['address_store']['county'] = $data[16];
		$pDataHash['address_store']['postcode'] = $data[17];
		$pDataHash['wande_store']['last_time'] = $data[18];
		$pDataHash['wande_store']['last_date'] = $data[19];
		$pDataHash['wande_store']['lockout_overide'] = $data[20];
		$pDataHash['wande_store']['lockout_state'] = $data[21];
		$pDataHash['wande_store']['notes'] = $data[22];
		$pDataHash['wande_store']['customer_number'] = $data[23];
		$pDataHash['wande_store']['birthday'] = $data[24];
		$pDataHash['wande_store']['changelog'] = $data[25];
		$pDataHash['wande_store']['country'] = $data[26];
		$pDataHash['address_store']['country'] = $data[26];
		$pDataHash['wande_store']['import_helper'] = $data[27];

		$this->mDb->StartTrans();
		$this->mContentId = 0;
		$pDataHash['content_id'] = 0;
		if ( LibertyContent::store( $pDataHash ) ) {
			$pDataHash['contact_store']['content_id'] = $pDataHash['content_id'];
			$pDataHash['contact_store']['address_id'] = $pDataHash['content_id'];
			$pDataHash['wande_store']['content_id'] = $pDataHash['content_id'];
			$pDataHash['address_store']['content_id'] = $pDataHash['content_id'];
			
			$result = $this->mDb->associateInsert( $ctable, $pDataHash['contact_store'] );
			$result = $this->mDb->associateInsert( $wtable, $pDataHash['wande_store'] );
			$result = $this->mDb->associateInsert( $atable, $pDataHash['address_store'] );
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
	function wandeDataExpunge()
	{
		$ret = FALSE;
		$query = "DELETE FROM `".BIT_DB_PREFIX."contact_wande`";
		$result = $this->mDb->query( $query );
//		$query = "DELETE FROM `".BIT_DB_PREFIX."address_phx`";
//		$result = $this->mDb->query( $query );
		return $ret;
	}
?>