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
	 * SageRecordLoad( $data ); 
	 * sage csv data import 
	 */
	function SageRecordLoad( &$data, $cltype = 1 ) {
		$ctable = BIT_DB_PREFIX."contact";
		$stable = BIT_DB_PREFIX."contact_sage";
		$atable = BIT_DB_PREFIX."contact_address";

		$pDataHash['sage_store']['cltype'] = $cltype;
		$pDataHash['address_store']['cltype'] = $cltype;
		$pDataHash['contact_store']['parent_id'] = 1;
		$pDataHash['contact_store']['xkey'] = $data[0];
		$pDataHash['sage_store']['usn'] = $data[0];
		$pDataHash['address_store']['sao'] = $data[0];
		$pDataHash['sage_store']['surname'] = $data[1];
		$pDataHash['title'] = $data[1];
		$pDataHash['sage_store']['organisation'] = $data[1];
		$pDataHash['address_store']['organisation'] = $data[1];
		$pDataHash['sage_store']['forename'] = '';
		$pDataHash['sage_store']['prefix'] = '';
		$pDataHash['address_store']['sao'] = '';
		$pDataHash['address_store']['pao'] = '';
		$pDataHash['address_store']['number'] = '';
		$pDataHash['address_store']['street'] = $data[2];
		$pDataHash['address_store']['locality'] = $data[3];
		$pDataHash['address_store']['town'] = $data[4];
		$pDataHash['address_store']['county'] = $data[5];
		$pDataHash['address_store']['pao'] = $data[6];
		$pDataHash['address_store']['postcode'] = substr( $data[6], 0, 9);
		$pDataHash['sage_store']['contact_name'] = $data[7];
		$pDataHash['sage_store']['telephone'] = $data[8];
		$pDataHash['sage_store']['fax'] = $data[9];
		$pDataHash['sage_store']['web'] = $data[9];
		$pDataHash['sage_store']['analysis_1'] = $data[10];
		$pDataHash['sage_store']['analysis_2'] = $data[11];
		$pDataHash['sage_store']['analysis_3'] = $data[12];
		$pDataHash['sage_store']['dept_number'] = $data[13];
		$pDataHash['sage_store']['vat_reg_number'] = $data[14];
		$pDataHash['sage_store']['turnover_mtd'] = $data[15];
		$pDataHash['sage_store']['turnover_ytd'] = $data[16];
		$pDataHash['sage_store']['turnover_prior'] = $data[17];
		$pDataHash['sage_store']['credit_limit'] = $data[18];
		$pDataHash['sage_store']['terms'] = $data[19];
		$pDataHash['sage_store']['settlement_due_days'] = $data[20];
		$pDataHash['sage_store']['settlement_disc_rate'] = $data[21];
		$pDataHash['sage_store']['def_nom_code'] = $data[22];
		$pDataHash['sage_store']['def_tax_code'] = $data[23];

		$this->mDb->StartTrans();
		$this->mContentId = 0;
		$pDataHash['content_id'] = 0;
		if ( LibertyContent::store( $pDataHash ) ) {
			$pDataHash['contact_store']['content_id'] = $pDataHash['content_id'];
			$pDataHash['sage_store']['content_id'] = $pDataHash['content_id'];
			$pDataHash['address_store']['content_id'] = $pDataHash['content_id'];
			$pDataHash['contact_store']['address_id'] = $pDataHash['content_id'];
			
			$result = $this->mDb->associateInsert( $ctable, $pDataHash['contact_store'] );
			$result = $this->mDb->associateInsert( $stable, $pDataHash['sage_store'] );
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
	function SageDataExpunge()
	{
		$ret = FALSE;
		$query = "DELETE FROM `".BIT_DB_PREFIX."contact_sage`";
		$result = $this->mDb->query( $query );
		$query = "DELETE FROM `".BIT_DB_PREFIX."contact_address` WHERE CLTYPE = 1 OR CLTYPE = 2";
		$result = $this->mDb->query( $query );
		return $ret;
	}
?>
