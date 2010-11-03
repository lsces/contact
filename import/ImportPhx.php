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
	function PhxRecordLoad( &$data ) {
		$table = BIT_DB_PREFIX."contact_phx";
		$atable = BIT_DB_PREFIX."contact_address";

		$usn = 10000 + $data[0];
		$pDataHash['contact_store']['contact_id'] = $data[0];
		$pDataHash['address_store']['contact_id'] = $data[0];
		$pDataHash['contact_store']['usn'] = $usn;
		$pDataHash['address_store']['usn'] = $usn;
		$pDataHash['contact_store']['surname'] = $data[1];
		$pDataHash['contact_store']['organisation'] = $data[3].' '.$data[1];
		$pDataHash['address_store']['organisation'] = $data[1];
		if ( $data[2] == 'D' ) $type = 0; else $type = 1;
		$pDataHash['contact_store']['uprn'] = $type;
		$pDataHash['address_store']['uprn'] = $type;
		$pDataHash['contact_store']['forename'] = $data[3];
		$pDataHash['contact_store']['prefix'] = '';
		$pDataHash['address_store']['sao'] = '';
		$pDataHash['address_store']['pao'] = '';
		$pDataHash['address_store']['number'] = '';
		$pDataHash['address_store']['street'] = $data[4];
		$pDataHash['address_store']['locality'] = $data[5];
		$pDataHash['address_store']['town'] = $data[6];
		$pDataHash['address_store']['county'] = $data[7];
		$pDataHash['address_store']['postcode'] = $data[8];
		$pDataHash['contact_store']['contact1'] = $data[9];
		$pDataHash['contact_store']['contact2'] = $data[10];
		$pDataHash['contact_store']['contact3'] = $data[11];
		$pDataHash['contact_store']['key1'] = $data[12];
		$pDataHash['contact_store']['tel1'] = $data[13];
		$pDataHash['contact_store']['key2'] = $data[14];
		$pDataHash['contact_store']['tel2'] = $data[15];
		$pDataHash['contact_store']['key3'] = $data[16];
		$pDataHash['contact_store']['tel3'] = $data[17];
		$pDataHash['contact_store']['passwd'] = $data[18];
		$pDataHash['contact_store']['prompt'] = $data[19];
		$pDataHash['contact_store']['memo'] = $data[20];
		$pDataHash['contact_store']['full_start_date'] = $data[21].'-'.$data[22].'-'.$data[23];
		$pDataHash['contact_store']['payment'] = $data[24];
		$pDataHash['contact_store']['maintain'] = $data[25];
		$pDataHash['contact_store']['code'] = $data[26];

		$this->mDb->StartTrans();
		$this->mContactId = 0;
//		$pDataHash['contact_store']['contact_id'] = $pDataHash['contact_id'];
//		$pDataHash['address_store']['contact_id'] = $pDataHash['contact_id'];
			
		$result = $this->mDb->associateInsert( $table, $pDataHash['contact_store'] );
		$result = $this->mDb->associateInsert( $atable, $pDataHash['address_store'] );
		$this->mDb->CompleteTrans();
/*		} else {
			$this->mDb->RollbackTrans();
			$this->mErrors['store'] = 'Failed to store this contact.';
		}				
*/
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
//		$query = "DELETE FROM `".BIT_DB_PREFIX."contact_address`";
//		$result = $this->mDb->query( $query );
		return $ret;
	}
?>