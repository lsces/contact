<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_contact/Contact.php,v 1.13 2010/04/18 02:27:23 wjames5 Exp $
 *
 * Copyright ( c ) 2006 bitweaver.org
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * @package contact
 */

/**
 * required setup
 */
require_once( LIBERTY_PKG_PATH.'LibertyContent.php' );		// Contact base class
require_once( NLPG_PKG_PATH.'lib/phpcoord-2.3.php' );

define( 'CONTACT_CONTENT_TYPE_GUID', 'contact' );

/**
 * @package contact
 */
class Contact extends LibertyBase {
	var $mContactId;
	var $mParentId;

	/**
	 * Constructor 
	 * 
	 * Build a Contact object based on LibertyContent
	 * @param integer Contact Id identifer
	 */
	function Contact( $pContactId = NULL ) {
		LibertyBase::LibertyBase();
		$this->mContactId = (int)$pContactId;
	}

	/**
	 * Load a Contact content Item
	 *
	 * (Describe Contact object here )
	 */
	function load($pContactId = NULL) {
		if ( $pContactId ) $this->mContactId = (int)$pContactId;
		if( $this->verifyId( $this->mContactId ) ) {
 			$query = "select ci.usn AS contact_id, ci.*
				FROM `".BIT_DB_PREFIX."contact` ci
				LEFT JOIN `".BIT_DB_PREFIX."contact_address` a ON a.contact_id = ci.usn
				LEFT JOIN `".BIT_DB_PREFIX."postcode` p ON p.`postcode` = a.`postcode`
				WHERE ci.`contact_id`=?";
/*
*/
			$result = $this->mDb->query( $query, array( $this->mContactId ) );

			if ( $result && $result->numRows() ) {
				$this->mInfo = $result->fields;
				$this->mContactId = (int)$result->fields['contact_id'];
				$this->mParentId = (int)$result->fields['usn'];
				$this->mContactName = $result->fields['title'];
				$this->mInfo['creator'] = (isset( $result->fields['creator_real_name'] ) ? $result->fields['creator_real_name'] : $result->fields['creator_user'] );
				$this->mInfo['editor'] = (isset( $result->fields['modifier_real_name'] ) ? $result->fields['modifier_real_name'] : $result->fields['modifier_user'] );
				$this->mInfo['display_url'] = $this->getDisplayUrl();
				$os1 = new OSRef($this->mInfo['x_coordinate'], $this->mInfo['y_coordinate']);
				$ll1 = $os1->toLatLng();
				$this->mInfo['prop_lat'] = $ll1->lat;
				$this->mInfo['prop_lng'] = $ll1->lng;
			}
		}
		return;
	}

	/**
	* verify, clean up and prepare data to be stored
	* @param $pParamHash all information that is being stored. will update $pParamHash by reference with fixed array of itmes
	* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	* @access private
	**/
	function verify( &$pParamHash ) {
		// make sure we're all loaded up if everything is valid
		if( $this->isValid() && empty( $this->mInfo ) ) {
			$this->load( TRUE );
		}

		// It is possible a derived class set this to something different
		if( empty( $pParamHash['content_type_guid'] ) ) {
			$pParamHash['content_type_guid'] = $this->mContentTypeGuid;
		}

		if( !empty( $this->mContactId ) ) {
			$pParamHash['contact_id'] = $this->mContactId;
		} else {
			unset( $pParamHash['contact_id'] );
		}

		if ( empty( $pParamHash['parent_id'] ) )
			$pParamHash['parent_id'] = $this->mContactId;
			
		// content store
		// check for name issues, first truncate length if too long
		if( empty( $pParamHash['surname'] ) || empty( $pParamHash['forename'] ) )  {
			$this->mErrors['names'] = 'You must enter a forename and surname for this contact.';
		} else {
			$pParamHash['title'] = substr( $pParamHash['prefix'].' '.$pParamHash['forename'].' '.$pParamHash['surname'].' '.$pParamHash['suffix'], 0, 160 );
			$pParamHash['content_store']['title'] = $pParamHash['title'];
		}	

		// Secondary store entries
		$pParamHash['contact_store']['prefix'] = $pParamHash['prefix'];
		$pParamHash['contact_store']['forename'] = $pParamHash['forename'];
		$pParamHash['contact_store']['surname'] = $pParamHash['surname'];
		$pParamHash['contact_store']['suffix'] = $pParamHash['suffix'];
		$pParamHash['contact_store']['organisation'] = $pParamHash['organisation'];

		if ( !empty( $pParamHash['nino'] ) ) $pParamHash['contact_store']['nino'] = $pParamHash['nino'];
		if ( !empty( $pParamHash['dob'] ) ) $pParamHash['contact_store']['dob'] = $pParamHash['dob'];
		if ( !empty( $pParamHash['eighteenth'] ) ) $pParamHash['contact_store']['eighteenth'] = $pParamHash['eighteenth'];
		if ( !empty( $pParamHash['dod'] ) ) $pParamHash['contact_store']['dod'] = $pParamHash['dod'];

		return( count( $this->mErrors ) == 0 );
	}

	/**
	* Store contact data
	* @param $pParamHash contains all data to store the contact
	* @param $pParamHash[title] title of the new contact
	* @param $pParamHash[edit] description of the contact
	* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	**/
	function store( &$pParamHash ) {
		if( $this->verify( $pParamHash ) ) {
			// Start a transaction wrapping the whole insert into liberty 

			$this->mDb->StartTrans();
			$table = BIT_DB_PREFIX."contact";

			if( $this->verifyId( $this->mContactId ) ) {
				if( !empty( $pParamHash['contact_store'] ) ) {
					$result = $this->mDb->associateUpdate( $table, $pParamHash['contact_store'], array( "contact_id" => $this->mContactId ) );
				}
				} else {
				$pParamHash['contact_store']['contact_id'] = $pParamHash['contact_id'];
				$pParamHash['contact_store']['usn'] = $pParamHash['contact_id'];
				if( isset( $pParamHash['contact_id'] ) && is_numeric( $pParamHash['contact_id'] ) ) {
					$pParamHash['contact_store']['usn'] = $pParamHash['contact_id'];
				} else {
					$pParamHash['contact_store']['usn'] = $this->mDb->GenID( 'contact_id_seq');
				}	
				$pParamHash['contact_store']['parent_id'] = $pParamHash['contact_store']['contact_id'];
				$this->mContactId = $pParamHash['contact_store']['contact_id'];
				$this->mParentId = $pParamHash['contact_store']['parent_id'];
				$result = $this->mDb->associateInsert( $table, $pParamHash['contact_store'] );
			}
			if ( $result ) {
				// load before completing transaction as firebird isolates results
				$this->load();
				$this->mDb->CompleteTrans();
			} else {
				$this->mDb->RollbackTrans();
				$this->mErrors['store'] = 'Failed to store this contact.';
			}
		}
		return( count( $this->mErrors ) == 0 );
	}

	/**
	 * Delete content object and all related records
	 */
	function expunge()
	{
		$ret = FALSE;
		if ($this->isValid() ) {
			$this->mDb->StartTrans();
			$query = "DELETE FROM `".BIT_DB_PREFIX."contact` WHERE `contact_id` = ?";
			$result = $this->mDb->query($query, array($this->mContactId ) );
			$query = "DELETE FROM `".BIT_DB_PREFIX."contact_type_map` WHERE `contact_id` = ?";
			$result = $this->mDb->query($query, array($this->mContactId ) );
			if (LibertyContent::expunge() ) {
			$ret = TRUE;
				$this->mDb->CompleteTrans();
			} else {
				$this->mDb->RollbackTrans();
			}
		}
		return $ret;
	}
    
	/**
	 * Returns Request_URI to a Contact content object
	 *
	 * @param string name of
	 * @param array different possibilities depending on derived class
	 * @return string the link to display the page.
	 */
	function getDisplayUrl( $pContactId=NULL ) {
		global $gBitSystem;
		if( empty( $pContactId ) ) {
			$pContactId = $this->mContactId;
		}

		return CONTACT_PKG_URL.'index.php?contact_id='.$pContactId;
	}

	/**
	 * Returns HTML link to display a Contact object
	 * 
	 * @param string Not used ( generated locally )
	 * @param array mInfo style array of content information
	 * @return the link to display the page.
	 */
	function getDisplayLink( $pText, $aux ) {
		if ( $this->mContactId != $aux['contact_id'] ) $this->load($aux['contact_id']);

		if (empty($this->mInfo['contact_id']) ) {
			$ret = '<a href="'.$this->getDisplayUrl($aux['contact_id']).'">'.$aux['title'].'</a>';
		} else {
			$ret = '<a href="'.$this->getDisplayUrl($aux['contact_id']).'">'."Contact - ".$this->mInfo['title'].'</a>';
		}
		return $ret;
	}

	/**
	 * Returns title of an Contact object
	 *
	 * @param array mInfo style array of content information
	 * @return string Text for the title description
	 */
	function getTitle( $pHash = NULL ) {
		$ret = NULL;
		if( empty( $pHash ) ) {
			$pHash = &$this->mInfo;
		} else {
			if ( $this->mContactId != $pHash['contact_id'] ) {
				$this->load($pHash['contact_id']);
				$pHash = &$this->mInfo;
			}
		}

		if( !empty( $pHash['title'] ) ) {
			$ret = "Contact - ".$this->mInfo['title'];
		} elseif( !empty( $pHash['content_name'] ) ) {
			$ret = $pHash['content_name'];
		}
		return $ret;
	}

	/**
	 * Returns list of contract entries
	 *
	 * @param integer 
	 * @param integer 
	 * @param integer 
	 * @return string Text for the title description
	 */
	function getList( &$pListHash ) {
		LibertyContent::prepGetList( $pListHash );
		
		$whereSql = $joinSql = $selectSql = '';
		$bindVars = array();
		
		if ( isset($pListHash['find']) ) {
			$findesc = '%' . strtoupper( $pListHash['find'] ) . '%';
			$whereSql .= " AND (UPPER(con.`SURNAME`) like ? or UPPER(con.`FORENAME`) like ?) ";
			array_push( $bindVars, $findesc );
		}

		if ( isset($pListHash['add_sql']) ) {
			$whereSql .= " AND $add_sql ";
		}

		$query = "SELECT con.*, 
				FROM `".BIT_DB_PREFIX."contact` ci 
				$joinSql
				WHERE $whereSql  
				order by ".$this->mDb->convertSortmode( $pListHash['sort_mode'] );
		$query_cant = "SELECT COUNT(ci.`contact_id`) FROM `".BIT_DB_PREFIX."contact` ci
				$joinSql
				WHERE $whereSql";

		$ret = array();
		$this->mDb->StartTrans();
		$result = $this->mDb->query( $query, $bindVars, $pListHash['max_records'], $pListHash['offset'] );
		$cant = $this->mDb->getOne( $query_cant, $bindVars );
		$this->mDb->CompleteTrans();

		while ($res = $result->fetchRow()) {
			$res['contact_url'] = $this->getDisplayUrl( $res['contact_id'] );
			$ret[] = $res;
		}

		$pListHash['cant'] = $cant;
		LibertyContent::postGetList( $pListHash );
		return $ret;
	}

	/**
	* Returns titles of the contact type table
	*
	* @return array List of contact type names from the contact mamanger in alphabetical order
	*/
	function getContactTypeList() {
		$query = "SELECT `type_name` FROM `contact_type`
				  ORDER BY `type_name`";
		$result = $this->mDb->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = trim($res["type_name"]);
		}
		return $ret;
	}

	/**
	 * ContactRecordLoad( $data ); 
	 * phx seurity file import 
	 */
	function ContactRecordLoad( &$data ) {
		$table = BIT_DB_PREFIX."contact";
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
	function DataExpunge()
	{
		$ret = FALSE;
		$query = "DELETE FROM `".BIT_DB_PREFIX."contact`";
		$result = $this->mDb->query( $query );
		$query = "DELETE FROM `".BIT_DB_PREFIX."contact_address`";
		$result = $this->mDb->query( $query );
		$query = "DELETE FROM `".BIT_DB_PREFIX."contact_xref`";
		$result = $this->mDb->query( $query );
		return $ret;
	}

	/**
	 * getContactList( &$pParamHash );
	 * Get list of contact records 
	 */
	function getContactList( &$pParamHash ) {
		global $gBitSystem, $gBitUser;
		
		if ( empty( $pParamHash['sort_mode'] ) ) {
			if ( empty( $_REQUEST["sort_mode"] ) ) {
				$pParamHash['sort_mode'] = 'surname_asc';
			} else {
			$pParamHash['sort_mode'] = $_REQUEST['sort_mode'];
			}
		}
		
		LibertyContent::prepGetList( $pParamHash );

		$findSql = '';
		$selectSql = '';
		$joinSql = '';
		$whereSql = '';
		$bindVars = array();
		$type = 'surname';
		
		// this will set $find, $sort_mode, $max_records and $offset
		extract( $pParamHash );

		if( isset( $find_org ) and is_string( $find_org ) and $find_org <> '' ) {
			$whereSql .= " AND UPPER( ci.`organisation` ) like ? ";
			$bindVars[] = '%' . strtoupper( $find_org ). '%';
			$type = 'organisation';
			$pParamHash["listInfo"]["ihash"]["find_org"] = $find_org;
		}
		if( isset( $find_name ) and is_string( $find_name ) and $find_name <> '' ) {
		    $split = preg_split('|[,. ]|', $find_name, 2);
			$whereSql .= " AND UPPER( ci.`surname` ) STARTING ? ";
			$bindVars[] = strtoupper( $split[0] );
		    if ( array_key_exists( 1, $split ) ) {
				$split[1] = trim( $split[1] );
				$whereSql .= " AND UPPER( ci.`forename` ) STARTING ? ";
				$bindVars[] = strtoupper( $split[1] );
			}
			$pParamHash["listInfo"]["ihash"]["find_name"] = $find_name;
		}
		if( isset( $find_street ) and is_string( $find_street ) and $find_street <> '' ) {
			$whereSql .= " AND UPPER( a.`street` ) like ? ";
			$bindVars[] = '%' . strtoupper( $find_street ). '%';
			$pParamHash["listInfo"]["ihash"]["find_street"] = $find_street;
		}
		if( isset( $find_org ) and is_string( $find_postcode ) and $find_postcode <> '' ) {
			$whereSql .= " AND UPPER( `a.postcode` ) LIKE ? ";
			$bindVars[] = '%' . strtoupper( $find_postcode ). '%';
			$pParamHash["listInfo"]["ihash"]["find_postcode"] = $find_postcode;
		}
		$query = "SELECT ci.*, a.UPRN, a.POSTCODE, a.SAO, a.PAO, a.NUMBER, a.STREET, a.LOCALITY, a.TOWN, a.COUNTY, ci.parent_id as uprn,
			(SELECT COUNT(*) FROM `".BIT_DB_PREFIX."contact_xref` x WHERE x.contact_id = ci.contact_id ) AS links, 
			(SELECT COUNT(*) FROM `".BIT_DB_PREFIX."task_ticket` e WHERE e.usn = ci.usn ) AS enquiries $selectSql 
			FROM `".BIT_DB_PREFIX."contact` ci 
			LEFT JOIN `".BIT_DB_PREFIX."contact_address` a ON a.contact_id = ci.contact_id $findSql
			$joinSql 
			WHERE ci.`".$type."` <> '' $whereSql ORDER BY ".$this->mDb->convertSortmode( $sort_mode );
		$query_cant = "SELECT COUNT( * )
			FROM `".BIT_DB_PREFIX."contact` ci
			LEFT JOIN `".BIT_DB_PREFIX."contact_address` a ON a.contact_id = ci.contact_id $findSql
			$joinSql WHERE ci.`".$type."` <> '' $whereSql ";
//			INNER JOIN `".BIT_DB_PREFIX."contact_address` a ON a.contact_id = ci.contact_id 
		$result = $this->mDb->query( $query, $bindVars, $max_records, $offset );
		$ret = array();
		while( $res = $result->fetchRow() ) {
			if (!empty($parse_split)) {
				$res = array_merge($this->parseSplit($res), $res);
			}
			$ret[] = $res;
		}
		$pParamHash["cant"] = $this->mDb->getOne( $query_cant, $bindVars );

		LibertyContent::postGetList( $pParamHash );
		return $ret;
	}


	/**
	 * loadContact( &$pParamHash );
	 * Get contact record 
	 */
	function loadContact( &$pParamHash = NULL ) {
		if( $this->isValid() ) {
		$sql = "SELECT ci.*, a.*, p.*
			FROM `".BIT_DB_PREFIX."contact` ci 
			LEFT JOIN `".BIT_DB_PREFIX."contact_address` a ON a.usn = ci.usn
			LEFT JOIN `".BIT_DB_PREFIX."postcode` p ON p.`postcode` = a.`postcode`
			WHERE ci.`contact_id` = ?";
			if( $rs = $this->mDb->query( $sql, array( $this->mContactId ) ) ) {
				if(	$this->mInfo = $rs->fields ) {
/*					if(	$this->mInfo['local_custodian_code'] == 0 ) {
						global $gBitSystem;
						$gBitSystem->fatalError( tra( 'You do not have permission to access this contact record' ), 'error.tpl', tra( 'Permission denied.' ) );
					}
*/

					$sql = "SELECT x.`last_update_date`, x.`source`, x.`cross_reference` 
							FROM `".BIT_DB_PREFIX."contact_xref` x
							WHERE x.contact_id = ?";
/* Link to legacy system
							CASE
							WHEN x.`source` = 'POSTFIELD' THEN (SELECT `USN` FROM `".BIT_DB_PREFIX."caller` c WHERE ci.`caller_id` = x.`cross_reference`)
							ELSE '' END AS USN 
							
 */

					$result = $this->mDb->query( $sql, array( $this->mContactId ) );

					while( $res = $result->fetchRow() ) {
						$this->mInfo['xref'][] = $res;
						if ( $res['source'] == 'POSTFIELD' ) $ticket[] = $res['cross_reference'];
					}
					if ( isset( $ticket ) )
					{ $sql = "SELECT t.* FROM `".BIT_DB_PREFIX."task_ticket` t 
							WHERE t.caller_id IN(". implode(',', array_fill(0, count($ticket), '?')) ." )";
						$result = $this->mDb->query( $sql, $ticket );
						while( $res = $result->fetchRow() ) {
							$this->mInfo['tickets'][] = $res;
						}
					}
					$os1 = new OSRef($this->mInfo['x_coordinate'], $this->mInfo['y_coordinate']);
					$ll1 = $os1->toLatLng();
					$this->mInfo['prop_lat'] = $ll1->lat;
					$this->mInfo['prop_lng'] = $ll1->lng;
//					$this->mInfo['display_usrn'] = $this->getUsrnEntryUrl( $this->mInfo['usrn'] );
//					$this->mInfo['display_uprn'] = $this->getUprnEntryUrl( $this->mInfo['uprn'] );
//vd($this->mInfo);
				} else {
					global $gBitSystem;
					$gBitSystem->fatalError( tra( 'Contact record does not exist' ), 'error.tpl', tra( 'Not found.' ) );
				}
			}
		}
		return( count( $this->mInfo ) );
	}


	/**
	 * getXrefList( &$pParamHash );
	 * Get list of xref records for this contact record
	 */
	function loadXrefList() {
		if( empty( $this->mInfo['xref'] ) ) {
		
			$sql = "SELECT x.`last_update_date`, x.`source`, s.`cross_ref_title` || '-' || x.`xorder` AS source_title, x.`cross_reference`, x.`data`, x.`xref_key` AS usn 
				FROM `".BIT_DB_PREFIX."contact_xref` x
				JOIN `".BIT_DB_PREFIX."contact_xref_source` s 
				ON s.`source` = x.`source`
				WHERE x.contact_id = ?
				ORDER BY x.`source`, x.`xorder`";

			$result = $this->mDb->query( $sql, array( $this->mContactId ) );

			while( $res = $result->fetchRow() ) {
				$this->mInfo['xref'][] = $res;
				if ( $res['source'] == 'POSTFIELD' ) $caller[] = $res['cross_reference'];
			}

			$sql = "SELECT t.* FROM `".BIT_DB_PREFIX."task_ticket` t 
				WHERE t.usn = ?";
			$result = $this->mDb->query( $sql, array( '9000000001' ) ); //$this->mContactId ) );
			while( $res = $result->fetchRow() ) {
				$this->mInfo['tickets'][] = $res;
			}

		}
	}

}
?>
