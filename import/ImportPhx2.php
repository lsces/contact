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
	 * PhxRecordLoad( $data ); 
	 * phx seurity file import 
	 */
	function PhxRecordLoad( &$data, $cnt ) {
		$ctable = BIT_DB_PREFIX."contact";
		$ptable = BIT_DB_PREFIX."contact_phx";
		$atable = BIT_DB_PREFIX."contact_address";

		$pDataHash['contact_store']['parent_id'] = 1;
		$pDataHash['contact_store']['xkey'] = $data[0];
		$pDataHash['title'] = $data[2];
		if ( strlen($data[4]) > 0 ) {
			$pDataHash['title'] = $data[4];
			if ( strlen($data[3]) > 0 ) $pDataHash['title'] .= ', '.$data[3].' '.$data[5];
			else if ( strlen($data[3]) > 0 ) $pDataHash['title'] .= ', '.$data[5];
			if ( strlen($data[6]) > 0 ) $pDataHash['title'] = trim($pDataHash['title']).' ('.$data[6].')';
		}
		$pDataHash['phx_store']['content_id'] = $cnt;
		$pDataHash['address_store']['content_id'] = $cnt;
		$pDataHash['phx_store']['contract'] = $data[0];
		if ( $data[1] == 'D' ) $type = 0; else $type = 1;
		$pDataHash['phx_store']['cltype'] = $type;
		$pDataHash['address_store']['cltype'] = $type;
		$pDataHash['phx_store']['organisation'] = $data[2];
		$pDataHash['phx_store']['prefix'] = $data[3];
		$pDataHash['phx_store']['surname'] = $data[4];
		$pDataHash['phx_store']['forename'] = $data[5];
		$pDataHash['phx_store']['spouse'] = $data[6];
		$pDataHash['address_store']['sao'] = '';
		$pDataHash['address_store']['pao'] = '';
		$pDataHash['address_store']['number'] = '';
		$pDataHash['address_store']['street'] = $data[7];
		$pDataHash['address_store']['locality'] = $data[8];
		$pDataHash['address_store']['town'] = $data[9];
		$pDataHash['address_store']['county'] = $data[10];
		$pDataHash['address_store']['postcode'] = $data[11];
		$pDataHash['phx_store']['contact1'] = $data[12];
		$pDataHash['phx_store']['cname2'] = $data[13];
		$pDataHash['phx_store']['contact2'] = $data[14];
		$pDataHash['phx_store']['cname3'] = $data[15];
		$pDataHash['phx_store']['contact3'] = $data[16];
		$pDataHash['phx_store']['key1'] = $data[17];
		$pDataHash['phx_store']['tel1'] = $data[18];
		$pDataHash['phx_store']['mob1'] = $data[19];
		$pDataHash['phx_store']['key2'] = $data[20];
		$pDataHash['phx_store']['tel2'] = $data[21];
		$pDataHash['phx_store']['mob2'] = $data[22];
		$pDataHash['phx_store']['key3'] = $data[23];
		$pDataHash['phx_store']['tel3'] = $data[24];
		$pDataHash['phx_store']['mob3'] = $data[25];
		$pDataHash['phx_store']['key4'] = $data[26];
		$pDataHash['phx_store']['tel4'] = $data[27];
		$pDataHash['phx_store']['mob4'] = $data[28];
		$pDataHash['phx_store']['passwd'] = $data[29];
		$pDataHash['phx_store']['prompt'] = $data[30];
		$pDataHash['phx_store']['email1'] = $data[31];
		$pDataHash['phx_store']['email2'] = $data[32];
		$pDataHash['phx_store']['memo'] = $data[33];
		$pDataHash['phx_store']['full_start_date'] = $data[34].'-'.$data[35].'-'.$data[36];
		$pDataHash['phx_store']['payment'] = $data[37];
		$pDataHash['phx_store']['maintain'] = $data[38];
		$pDataHash['phx_store']['code'] = $data[39];
		$pDataHash['phx_store']['key_seal'] = $data[40];
		$pDataHash['phx_store']['break_seal'] = $data[41];

		$this->mDb->StartTrans();
		$this->mContentId = 0;
		$pDataHash['content_id'] = 0;
		if ( LibertyContent::store( $pDataHash ) ) {
			$pDataHash['contact_store']['content_id'] = $pDataHash['content_id'];
			$pDataHash['contact_store']['address_id'] = $pDataHash['content_id'];
			$pDataHash['phx_store']['content_id'] = $pDataHash['content_id'];
			$pDataHash['address_store']['content_id'] = $pDataHash['content_id'];
			
			$result = $this->mDb->associateInsert( $ctable, $pDataHash['contact_store'] );
			$result = $this->mDb->associateInsert( $ptable, $pDataHash['phx_store'] );
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
	function PhxDataExpunge()
	{
		$ret = FALSE;
		$query = "DELETE FROM `".BIT_DB_PREFIX."contact_phx`";
		$result = $this->mDb->query( $query );
//		$query = "DELETE FROM `".BIT_DB_PREFIX."address_phx`";
//		$result = $this->mDb->query( $query );
		return $ret;
	}

?>