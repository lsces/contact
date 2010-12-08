<?php
/**
 * @version $Header$
 * @package articles
 * 
 * @copyright Copyright (c) 2004-2006, bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 */

/**
 * Required setup
 */
global $gBitSystem;
require_once( CONTACT_PKG_PATH.'ContactXrefType.php' );

/**
 * @package contact
 */
class ContactXref extends BitBase {
	var $mType;
	var $mSource;
	var $mXrefId;
	var $mContentId;
	var $mDate;

	function ContactXref( $iXrefId = NULL ) {
		$this->mXrefId = NULL;
		$this->mSource = NULL;
		BitBase::BitBase();
		if( $iXrefId ) {
			$this->load( $iXrefId );
		}

		// Date object to handle date and time display
		$this->mDate = new BitDate();
		$offset = $this->mDate->get_display_offset();
	}

	function isValid() {
		return ($this->verifyId($this->mXrefId));
	}

	function load( $pXref_id = NULL ) {
		if( @BitBase::verifyId( $pXref_id ) ) {
			$sql = "SELECT x.*, CASE
					WHEN x.`xorder` = 0 THEN s.`cross_ref_title`
					ELSE s.`cross_ref_title` || '-' || x.`xorder` END
					AS source_title, s.`source`, s.`xref_type`,
					CASE WHEN x.`start_date` IS NULL THEN 'y' ELSE 'n' END AS `ignore_start_date`, 
					CASE WHEN x.`end_date` IS NULL THEN 'y' ELSE 'n' END AS `ignore_end_date` 
					FROM `".BIT_DB_PREFIX."contact_xref` x
					JOIN `".BIT_DB_PREFIX."contact_xref_source` s ON s.`source` = x.`source`
					WHERE x.`xref_id` = ?
					ORDER BY x.`xorder`";
			$result = $this->mDb->getRow( $sql, array(  $pXref_id ) );
			if( $result['content_id'] ) {
				$this->mXrefId = $pXref_id;
				$this->mContentId = $result['content_id'];
				$this->mType = $result['xref_type'];
				$this->mSource = $result['source'];
				$this->mInfo['title'] = $result['source_title'];
				unset($result['source_title']);
				$this->mInfo['data'] = $result;
			}
		}
	}

	function verify( &$pParamHash ) {
//		if ( $this->isValid() ) {
			// Validate the (optional) xref_id parameter
			if (@$this->verifyId($pParamHash['xref_id'])) {
				$pParamHash['xref_id'] = (int)$pParamHash['xref_id'];
			} else {
				$pParamHash['xref_id'] = NULL;
			}

			if ( isset ( $pParamHash['fAddXref'] ) ) {
				if ( isset( $pParamHash['Array_xref_type_list'] )) {
					$pParamHash['xref_store']['source'] = $pParamHash['Array_xref_type_list']['Array.source'];
				}
				$pParamHash['xref_store']['content_id'] = $pParamHash['content_id'];
				$sql = "SELECT x`.multi` FROM `".BIT_DB_PREFIX."contact_xref_source` x WHERE x.`source` = ?";				
				$pParamHash['xref_store']['xorder'] = $this->mDb->getOne( $sql, array(  $pParamHash['xref_store']['source'] ) );
			} 

			if ( isset ( $pParamHash['fStepXref']  ) ) {
				$pParamHash['xref_store']['source'] = $this->mSource;
				$pParamHash['xref_store']['xorder'] = $this->mInfo['data']['xorder'] + 1;
				$pParamHash['xref_store']['content_id'] =  $this->mContentId;
				$pParamHash['start_date'] = $this->mDb->NOW();
				$pParamHash['ignore_end_date'] = 'on';
				$pParamHash['xref_store']['xkey'] = '';
				$pParamHash['xref_store']['xkey_ext'] = '';
				$pParamHash['xref_store']['data'] = '';
			}
			
			if ( isset( $pParamHash['xkey'] )) {
				$pParamHash['xref_store']['xkey'] = $pParamHash['xkey'];
			} 
			if ( isset( $pParamHash['xkey_ext'] )) {
				$pParamHash['xref_store']['xkey_ext'] = $pParamHash['xkey_ext'];
			} 
			if ( isset( $pParamHash['edit'] )) {
				$pParamHash['xref_store']['data'] = $pParamHash['edit'];
			} 
			$pParamHash['xref_store']['last_update_date'] = $this->mDb->NOW();

			// If start and/or end dates are supplied these are updated as well
			if( !empty( $pParamHash['start_Month'] ) ) {
				$dateString = $this->mDate->gmmktime(
					$pParamHash['start_Hour'],
					$pParamHash['start_Minute'],
					isset($pParamHash['start_Second']) ? $pParamHash['start_Second'] : 0,
					$pParamHash['start_Month'],
					$pParamHash['start_Day'],
					$pParamHash['start_Year']
				);
	
				$timestamp = $this->mDate->getUTCFromDisplayDate( $dateString );
				if( $timestamp !== -1 ) {
					$pParamHash['start_date'] = $timestamp;
				}
			}
			if( !empty( $pParamHash['start_date'] ) ) {
				$pParamHash['xref_store']['start_date'] = $pParamHash['start_date'];
			}
			if( isset ($pParamHash['ignore_start_date']) && $pParamHash['ignore_start_date'] == 'on' ) {
				$pParamHash['xref_store']['start_date'] = '';
			}
	
			if( !empty( $pParamHash['end_Month'] ) ) {
				$dateString = $this->mDate->gmmktime(
					$pParamHash['end_Hour'],
					$pParamHash['end_Minute'],
					isset($pParamHash['end_Second']) ? $pParamHash['end_Second'] : 0,
					$pParamHash['end_Month'],
					$pParamHash['end_Day'],
					$pParamHash['end_Year']
				);
	
				$timestamp = $this->mDate->getUTCFromDisplayDate( $dateString );
				if( $timestamp !== -1 ) {
					$pParamHash['end_date'] = $timestamp;
				}
			}
			if( !empty( $pParamHash['end_date'] ) ) {
				$pParamHash['xref_store']['end_date'] = $pParamHash['end_date'];
			}
			if( isset ($pParamHash['ignore_end_date']) && $pParamHash['ignore_end_date'] == 'on' ) {
				$pParamHash['xref_store']['end_date'] = '';
			}
//		}
		return(count($this->mErrors) == 0);
	}

	function store( &$pParamHash = NULL ) {
		if( $this->verify( $pParamHash ) ) {
			$table = BIT_DB_PREFIX."contact_xref";

			$this->mDb->StartTrans();
			if( isset( $pParamHash['xref_id'] ) ) {
				$result = $this->mDb->associateUpdate( $table, $pParamHash['xref_store'], array( "xref_id" => $pParamHash['xref_id'] ) );
			} else {
				$this->mXrefId = $pParamHash['ref_id'] = $pParamHash['xref_store']['xref_id'] = $this->mDb->GenID( 'contact_xref_seq' );
				$result = $this->mDb->associateInsert( $table, $pParamHash['xref_store'] );
			}
			// load before completing transaction as firebird isolates results
			$this->load( $this->mXrefId );
			$this->mDb->CompleteTrans();
			return true;
		} else {
			return false;
		}
	}

	function stepXref( &$pParamHash = NULL ) {
		if ( $pParamHash["expunge"] == 2 ) {
			$pParamHash['end_date'] = $this->mDb->NOW();
			$this->store( $pParamHash );
			unset($pParamHash['xref_id']);
			$pParamHash['fStepXref'] = 1;
		} else if ( $pParamHash["expunge"] == 1 ) {
			$pParamHash['end_date'] = $this->mDb->NOW();
		} else {
			$pParamHash['ignore_end_date'] = 'on';
		}
		$this->store( $pParamHash );
		return true;
	}
	
}
?>
