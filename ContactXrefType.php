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
require_once( KERNEL_PKG_PATH."BitBase.php" );

/**
 * @package contact
 */
class ContactXrefType extends BitBase {
	var $mSource;
	var $mXrefId;
	var $mDate;

	function ContactXrefType($iTypeId = NULL, $iSource = NULL) {
		$this->mSource = NULL;
		BitBase::BitBase();
		if ( $iTypeId || $iSource ) {
			$this->loadXrefType(array('type_id'=>$iTypeId, 'source'=>$iSou4ce));
		}
	}

	function isValid() {
		return ($this->verifyId($this->mXrefId));
	}

	function loadXref( $pXref_id = NULL ) {
		if( @BitBase::verifyId( $pXref_id ) ) {
			$sql = "SELECT x.*, CASE
					WHEN x.`xorder` = 0 THEN s.`cross_ref_title`
					ELSE s.`cross_ref_title` || '-' || x.`xorder` END
					AS source_title
					FROM `".BIT_DB_PREFIX."contact_xref` x
					JOIN `".BIT_DB_PREFIX."contact_xref_source` s ON s.`source` = x.`source`
					WHERE x.`xref_id` = ?
					ORDER BY x.`xorder`";
			$result = $this->mDb->getRow( $sql, array(  $pXref_id ) );
			if( $result['content_id'] ) {
				$this->load( $result['content_id'] );
				$this->mInfo['xref_title'] = $result['source_title'];
				unset($result['source_title']);
				$this->mInfo['xref_store'] = $result;
			}
		}
	}

	function verify( &$pParamHash ) {
		if ( $this->isValid() ) {
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
				$pParamHash['xref_store']['xorder'] = 0;
			} 
			if ( isset( $pParamHash['xref'] )) {
				$pParamHash['xref_store']['xref'] = $pParamHash['xref'];
			} else {
				$pParamHash['xref_store']['xref'] = '';
			}
			if ( isset( $pParamHash['xkey'] )) {
				$pParamHash['xref_store']['xkey'] = $pParamHash['xkey'];
			} else {
				$pParamHash['xref_store']['xref'] = '';
			}
			if ( isset( $pParamHash['xkey_ext'] )) {
				$pParamHash['xref_store']['xkey_ext'] = $pParamHash['xkey_ext'];
			} else {
				$pParamHash['xref_store']['xref'] = '';
			}
			if ( isset( $pParamHash['edit'] )) {
				$pParamHash['xref_store']['data'] = $pParamHash['edit'];
			} else {
				$pParamHash['xref_store']['xref'] = '';
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
		}
		return(count($this->mErrors) == 0);
	}

	function storeXref( $pParamHash = NULL ) {
		if( $this->verify( $pParamHash ) ) {
			$table = BIT_DB_PREFIX."contact_xref";

			$this->mDb->StartTrans();
			if( !empty( $pParamHash['xref_id'] ) ) {
				$result = $this->mDb->associateUpdate( $table, $pParamHash['xref_store'], array( "xref_id" => $pParamHash['xref_id'] ) );
			} else {
				$result = $this->mDb->associateInsert( $table, $pParamHash['xref_store'] );
			}
			// load before completing transaction as firebird isolates results
			$this->load();
			$this->mDb->CompleteTrans();
			return true;
		} else {
			return false;
		}
	}

	function getXrefTypeList( $pOptionHash=NULL ) {
		global $gBitSystem;

		$where = '';
		$bindVars = array();

		$query = "SELECT x.*
				 FROM `".BIT_DB_PREFIX."contact_xref_source` x
				 WHERE x.`xref_type` > 0 $where ORDER BY x.`cross_ref_title`";

		$result = $gBitSystem->mDb->query( $query, $bindVars );

        $ret = array();

        while( $res = $result->fetchRow() ) {
           $ret[] = $res;
        }

        return $ret;
    }

	function getContactTypeList( $pOptionHash=NULL ) {
		global $gBitSystem;

		$where = '';
		$bindVars = array();

		$query = "SELECT x.*
				 FROM `".BIT_DB_PREFIX."contact_xref_source` x
				 WHERE x.`xref_type` = 0 $where ORDER BY x.`cross_ref_title`";

		$result = $gBitSystem->mDb->query( $query, $bindVars );

        $ret = array();

        while( $res = $result->fetchRow() ) {
           $ret[] = $res;
        }

        return $ret;
    }

	function getXrefTypeContactsList() {
		if (!$this->mTopicId) {
			return NULL;
		}

		$sql = "SELECT con.`content_id` FROM `".BIT_DB_PREFIX."contact` con
				JOIN `".BIT_DB_PREFIX."contact_xref` x ON x.`content_id` = con.`content_id` AND x.`source` = ?";
		$rs = $this->mDb->query($sql, array($this->mTopicId));

		$ret = array();
		while ($row = $rs->fetchRow()) {
			$ret[] = $row;
		}
		return $ret;
	}
}
?>
