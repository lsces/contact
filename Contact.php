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
require_once( CONTACT_PKG_PATH.'ContactXref.php' );
require_once( CONTACT_PKG_PATH.'ContactType.php' );
require_once( LIBERTY_PKG_PATH.'LibertyContent.php' );		// Contact base class
require_once( CONTACT_PKG_PATH.'lib/phpcoord-2.3.php' );

define( 'CONTACT_CONTENT_TYPE_GUID', 'contact' );

/**
 * @package contact
 */
class Contact extends LibertyContent {
	var $mParentId;
	var $mDate;
	var $mTypes;

	/**
	 * Constructor 
	 * 
	 * Build a Contact object based on LibertyContent
	 * @param integer Contact Id identifer
	 * @param integer Base content_id identifier 
	 */
	function Contact( $pContactId = NULL, $pContentId = NULL ) {
		LibertyContent::LibertyContent();
		$this->registerContentType( CONTACT_CONTENT_TYPE_GUID, array(
				'content_type_guid' => CONTACT_CONTENT_TYPE_GUID,
				'content_name' => 'Contact Entry',
				'handler_class' => 'Contact',
				'handler_package' => 'contact',
				'handler_file' => 'Contact.php',
				'maintainer_url' => 'http://lsces.co.uk'
			) );
		$this->mContentId = (int)$pContentId;
		$this->mContentTypeGuid = CONTACT_CONTENT_TYPE_GUID;

		// Date object to handle date and time display
		$this->mDate = new BitDate();
		$offset = $this->mDate->get_display_offset();

		// Permission setup
		$this->mViewContentPerm  = 'p_contact_view';
		$this->mCreateContentPerm  = 'p_contact_create';
		$this->mUpdateContentPerm  = 'p_contact_update';
		$this->mExpungeContentPerm  = 'p_contact_expunge';
		$this->mAdminContentPerm = 'p_contact_admin';
		
		$this->mTypes = new ContactType();
		$this->mTypes->setup();
	}

	/**
	 * Load a Contact content Item
	 *
	 * (Describe Contact object here )
	 */
	function load($pContentId = NULL) {
		if ( $pContentId ) $this->mContentId = (int)$pContentId;
		if( $this->verifyId( $this->mContentId ) ) {
 			$query = "select con.*, lc.*,
 				ap.*, xhC.`xkey_ext` AS house,
 				x00.`xkey_ext` as name, lc.`title` as organisation,
 				xhL.`xkey` as x_coordinate, xhL.`xkey_ext` as y_coordinate,
 				uue.`login` AS modifier_user, uue.`real_name` AS modifier_real_name,
				uuc.`login` AS creator_user, uuc.`real_name` AS creator_real_name
				FROM `".BIT_DB_PREFIX."contact` con
				LEFT JOIN `".BIT_DB_PREFIX."liberty_content` lc ON lc.content_id = con.content_id
				LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON (uue.`user_id` = lc.`modifier_user_id`)
				LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON (uuc.`user_id` = lc.`user_id`)
				LEFT JOIN `".BIT_DB_PREFIX."contact_xref` x00 ON x00.`content_id` = con.`content_id` AND x00.`source` = '$00'
				LEFT JOIN `".BIT_DB_PREFIX."contact_xref` xhC ON xhC.`content_id` = con.`content_id` AND xhC.`source` = '#C' AND ( xhC.`end_date` IS NULL OR xhC.`end_date` > CURRENT_TIMESTAMP )
				LEFT JOIN `".BIT_DB_PREFIX."contact_xref` xhL ON xhL.`content_id` = con.`content_id` AND xhL.`source` = '#L' AND ( xhL.`end_date` IS NULL OR xhL.`end_date` > CURRENT_TIMESTAMP )
				LEFT JOIN `".BIT_DB_PREFIX."address_postcode` ap ON ap.`postcode` = xhC.`xkey`
				WHERE con.`content_id`=?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
//				LEFT JOIN `".BIT_DB_PREFIX."contact` ci ON ci.contact_id = pro.owner_id
//				LEFT JOIN `".BIT_DB_PREFIX."contact_address` a ON a.contact_id = pro.address_id
//				LEFT JOIN `".BIT_DB_PREFIX."postcode` p ON p.`postcode` = a.`postcode`

			if ( $result && $result->numRows() ) {
				$this->mInfo = $result->fields;
				$this->mContentId = (int)$result->fields['content_id'];
//				$this->mParentId = (int)$result->fields['usn'];
				$this->mContactName = $result->fields['title'];
				$this->mInfo['creator'] = (isset( $result->fields['creator_real_name'] ) ? $result->fields['creator_real_name'] : $result->fields['creator_user'] );
				$this->mInfo['editor'] = (isset( $result->fields['modifier_real_name'] ) ? $result->fields['modifier_real_name'] : $result->fields['modifier_user'] );
				$this->mInfo['display_url'] = $this->getDisplayUrl();
				$this->mInfo['organisation'] = trim($this->mInfo['organisation']);
				$name = explode( '|', $this->mInfo['name'] );
				if ( isset( $name[0] ) ) { $this->mInfo['prefix'] = $name[0]; } else { $this->mInfo['prefix'] = ''; }
				if ( isset( $name[1] ) ) { $this->mInfo['forename'] = $name[1]; } else { $this->mInfo['forename'] = ''; }
				if ( isset( $name[2] ) ) { $this->mInfo['surname'] = $name[2]; } else { $this->mInfo['surname'] = ''; }
				if ( isset( $name[3] ) ) { $this->mInfo['suffix'] = $name[3]; } else { $this->mInfo['suffix'] = ''; }
				$this->mInfo['name'] = $this->mInfo['prefix'];
				$this->mInfo['name'] = trim($this->mInfo['name']).' '.$this->mInfo['forename'];
				$this->mInfo['name'] = trim($this->mInfo['name']).' '.$this->mInfo['surname'];
				$this->mInfo['name'] = trim($this->mInfo['name']).' '.$this->mInfo['suffix'];
				$this->mInfo['name'] = trim($this->mInfo['name']);

				if ( !$this->mInfo['x_coordinate'] and $this->mInfo['postcode'] and $this->mInfo['grideast'] <> '00000' ) {
					$os1 = new OSRef( $this->mInfo['grideast']*10, $this->mInfo['gridnorth']*10 );
					$ll1 = $os1->toLatLng();
					$this->mInfo['y_coordinate'] = $ll1->lat;
					$this->mInfo['x_coordinate'] = $ll1->lng;
				}

				$this->loadContentTypeList();
				if ( $this->mInfo['contact_types'][2]['content_id'] ) { 
					$this->loadClientList();
				}
				$this->loadXrefList();
				$this->loadAddressList();
			}
		}
		LibertyContent::load();
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

		if( !empty( $this->mContentId ) ) {
			$pParamHash['content_id'] = $this->mContentId;
			$pParamHash['contact_store']['content_id'] = $this->mContentId;
		} else {
			unset( $pParamHash['content_id'] );
		}

		$pParamHash['name'] = $pParamHash['prefix'].'|'.$pParamHash['forename'].'|'.$pParamHash['surname'].'|'.$pParamHash['suffix'];

		$pParamHash['title'] = $pParamHash['organisation'];
		if ( strlen($pParamHash['surname']) > 0 ) {
			$pParamHash['title'] = $pParamHash['surname'];
			if ( strlen($pParamHash['prefix']) > 0 ) $pParamHash['title'] .= ', '.$pParamHash['prefix'].' '.$pParamHash['forename'];
			else if ( strlen($pParamHash['forename']) > 0 ) $pParamHash['title'] .= ', '.$pParamHash['forename'];
		}
		$pParamHash['title'] = trim( $pParamHash['title'] );
		$pParamHash['contact_store']['xkey'] = $pParamHash['xkey'];
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
			if ( LibertyContent::store( $pParamHash ) ) {
				$table = BIT_DB_PREFIX."contact";
				$atable = BIT_DB_PREFIX."contact_address";

				// mContentId will not be set until the secondary data has commited 
				if( !empty( $pParamHash['contact_store']['content_id'] ) ) {
					$result = $this->mDb->associateUpdate( $table, $pParamHash['contact_store'], array( "content_id" => $this->mContentId ) );
				} else {
					$pParamHash['contact_store']['content_id'] = $pParamHash['content_id'];
					$pParamHash['contact_store']['parent_id'] = $pParamHash['content_id'];
					$pParamHash['contact_store']['address_id'] = $pParamHash['content_id'];
					$pParamHash['contact_store']['xkey'] = $pParamHash['xkey'];
					$this->mParentId = $pParamHash['contact_store']['parent_id'];
					$this->mContentId = $pParamHash['content_id'];
					$result = $this->mDb->associateInsert( $table, $pParamHash['contact_store'] );
					// Dummy contact addresss entry ... need edit page for address without using nlpg data
					unset($pParamHash['contact_store']['parent_id']);
					unset($pParamHash['contact_store']['xkey']);
					$result = $this->mDb->associateInsert( $atable, $pParamHash['contact_store'] );
				}
				if( !empty( $pParamHash['contact_types'] ) ) {
					$query = "DELETE FROM `".BIT_DB_PREFIX."contact_xref` WHERE `content_id` = ? AND `source` LIKE '$%'";
					$result = $this->mDb->query($query, array($this->mContentId ) );
					 foreach ( $pParamHash['contact_types'] as $key => $source ) {
						if ( $source == '$00' ) {
							$query = "INSERT INTO `".BIT_DB_PREFIX."contact_xref` (`content_id`, `source`, `xkey_ext`, `last_update_date`) VALUES ( ?, ?, ?, NULL )";
							$result = $this->mDb->query($query, array( $this->mContentId, $source, $pParamHash['name'] ) );
						} else if ( $source == '$01' ) {
							$query = "INSERT INTO `".BIT_DB_PREFIX."contact_xref` (`content_id`, `source`, `xkey_ext`, `last_update_date`) VALUES ( ?, ?, ?, NULL )";
							$result = $this->mDb->query($query, array( $this->mContentId, $source, $pParamHash['organisation'] ) );
						} else {
						$query = "INSERT INTO `".BIT_DB_PREFIX."contact_xref` (`content_id`, `source`, `last_update_date`) VALUES ( ?, ?, NULL )";
						$result = $this->mDb->query($query, array( $this->mContentId, $source ) );
						}
					 }
				}
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
			$query = "DELETE FROM `".BIT_DB_PREFIX."contact_xref` WHERE `content_id` = ?";
			$result = $this->mDb->query($query, array($this->mContentId ) );
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
	 * Check if the current post can have comments attached to it
	 */
	function isCommentable(){
		global $gBitSystem;	
		return TRUE; // $gBitSystem->isFeatureActive( 'contact_post_comments' );
	}

	/**
	 * Returns Request_URI to a Contact content object
	 *
	 * @param string name of
	 * @param array different possibilities depending on derived class
	 * @return string the link to display the page.
	 */
	function getDisplayUrl( $pContentId=NULL ) {
		global $gBitSystem;
		if( empty( $pContentId ) ) {
			$pContentId = $this->mContentId;
		}

		return CONTACT_PKG_URL.'index.php?content_id='.$pContentId;
	}

	/**
	 * Returns HTML link to display a Contact object
	 * 
	 * @param string Not used ( generated locally )
	 * @param array mInfo style array of content information
	 * @return the link to display the page.
	 */
	function getDisplayLink( $pText, $aux ) {
		if ( $this->mContentId != $aux['content_id'] ) $this->load($aux['content_id']);

		if (empty($this->mInfo['content_id']) ) {
			$ret = '<a href="'.$this->getDisplayUrl($aux['content_id']).'">'.$aux['title'].'</a>';
		} else {
			$ret = '<a href="'.$this->getDisplayUrl($aux['content_id']).'">'."Contact - ".$this->mInfo['title'].'</a>';
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
			if ( $this->mContentId != $pHash['content_id'] ) {
				$this->load($pHash['content_id']);
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
	 * Returns list of contact entries
	 *
	 * @param integer 
	 * @return array of contact entries
	 */
	function getList( &$pParamHash ) {
		global $gBitSystem, $gBitUser;
		
		LibertyContent::prepGetList( $pParamHash );

		$findSql = '';
		$selectSql = '';
		$joinSql = '';
		$whereSql = '';
		$bindVars = array();

		if ( isset( $pParamHash['role_id'] ) ) {
			array_push( $bindVars, $this->mContentTypeGuid );
			if ( $pParamHash['role_id'] > 0 ) {
				$whereSql .= " AND con.`role_id` = ? ";
				$bindVars[] = $pParamHash['role_id'];
			}
		}
		elseif ( isset( $pParamHash['contact_type_guid'][0] ) ) {
			$joinSql .= "JOIN `".BIT_DB_PREFIX."contact_xref` cx ON cx.`content_id` = con.`content_id` AND cx.`source` = ? ";
			$bindVars[] = $pParamHash['contact_type_guid'][0];
			array_push( $bindVars, $this->mContentTypeGuid );
		}
		
		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars, NULL, $pParamHash );

		// this will set $find, $sort_mode, $max_records and $offset
		extract( $pParamHash );

//		$pParamHash["listInfo"]["ihash"]['contact_type_guid'] = $contact_type_guid;

		$t = $gBitSystem->getUTCTime();
		if ( isset( $active ) ) {
			if ( $active === 'Inactive' ) {
				$whereSql .= " AND ( lc.`event_time` > 0 AND lc.`event_time` < $t ) ";
			} 
		} else {
			$active = 'Active';
		}
		if ( $active == 'Active' ) {
			$whereSql .= " AND ( lc.`event_time` = 0 OR lc.`event_time` > $t ) ";
		} 
		$pParamHash["listInfo"]["active"] = $active;

		if( isset( $find_key ) and is_string( $find_key ) and $find_key <> '' ) {
			$whereSql .= " AND UPPER( con.`xkey` ) = ? ";
			$bindVars[] = strtoupper( $find_key );
			$pParamHash["listInfo"]["ihash"]["find_key"] = $find_key;
			$find_title = '';
			$find_location = '';
			$find_postcode = '';
		}

		if( isset( $find_title ) and is_string( $find_title ) and $find_title <> '' ) {
			$whereSql .= " AND UPPER( lc.`title` ) like ? ";
			$bindVars[] = '%' . strtoupper( $find_title ). '%';
			$pParamHash["listInfo"]["ihash"]["find_title"] = $find_title;
		}

/*		if( isset( $find_name ) and is_string( $find_name ) and $find_name <> '' ) {
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
*/
		if( isset( $find_location ) and is_string( $find_location ) and $find_location <> '' ) {
			$whereSql .= " AND ( UPPER( ap.`add2` ) like ? OR UPPER( ap.`add3` ) like ? OR UPPER( ap.`add4` ) like ? OR UPPER( ap.`town` ) like ? )";
			$uploc = '%' . strtoupper( $find_location ). '%';;
			$bindVars[] = $uploc;
			$bindVars[] = $uploc;
			$bindVars[] = $uploc;
			$bindVars[] = $uploc;
			$pParamHash["listInfo"]["ihash"]["find_location"] = $find_location;
		}

		if( isset( $find_postcode ) and is_string( $find_postcode ) and $find_postcode <> '' ) {
			$whereSql .= " AND UPPER( `ap.postcode` ) LIKE ? ";
			$bindVars[] = '%' . strtoupper( $find_postcode ). '%';
			$pParamHash["listInfo"]["ihash"]["find_postcode"] = $find_postcode;
		}

		$query = "SELECT con.`content_id` as content_id, con.*, lc.*,
			ap.*, xhC.`xkey_ext` AS house,
			xhL.`xkey` as x_coordinate, xhL.`xkey_ext` as y_coordinate,
 			(SELECT COUNT(*) FROM `".BIT_DB_PREFIX."contact_xref` x WHERE x.`content_id` = con.`content_id` AND x.`source` NOT STARTING WITH '$' ) AS refs
			FROM `".BIT_DB_PREFIX."contact` con
			LEFT JOIN `".BIT_DB_PREFIX."liberty_content` lc ON lc.`content_id` = con.`content_id`
			LEFT JOIN `".BIT_DB_PREFIX."contact_xref` xhC ON xhC.`content_id` = con.`content_id` AND xhC.`source` = '#C' AND ( xhC.`end_date` IS NULL OR xhC.`end_date` > CURRENT_TIMESTAMP )
			LEFT JOIN `".BIT_DB_PREFIX."contact_xref` xhL ON xhL.`content_id` = con.`content_id` AND xhL.`source` = '#L' AND ( xhL.`end_date` IS NULL OR xhL.`end_date` > CURRENT_TIMESTAMP )
			LEFT JOIN `".BIT_DB_PREFIX."address_postcode` ap ON ap.`postcode` = xhC.`xkey`
			$findSql
			$joinSql 
			WHERE lc.`content_type_guid` = ? $whereSql
			ORDER BY ".$this->mDb->convertSortmode( $sort_mode );

//			(SELECT COUNT(*) FROM `".BIT_DB_PREFIX."task_ticket` e WHERE e.usn = ci.usn ) AS enquiries $selectSql 
		$query_cant = "SELECT COUNT( * )
			FROM `".BIT_DB_PREFIX."contact` con
			LEFT JOIN `".BIT_DB_PREFIX."liberty_content` lc ON lc.content_id = con.content_id
			LEFT JOIN `".BIT_DB_PREFIX."contact_xref` xhC ON xhC.`content_id` = con.`content_id` AND xhC.`source` = '#C' AND ( xhC.`end_date` IS NULL OR xhC.`end_date` > CURRENT_TIMESTAMP )
			LEFT JOIN `".BIT_DB_PREFIX."address_postcode` ap ON ap.`postcode` = xhC.`xkey`
			$joinSql WHERE lc.`content_type_guid` = ? $whereSql ";
		$result = $this->mDb->query( $query, $bindVars, $max_records, $offset );

		$ret = array();
		while( $res = $result->fetchRow() ) {
			if (!empty($parse_split)) {
				$res = array_merge($this->parseSplit($res), $res);
			}
			$ret[] = $res;
		}
		$pParamHash["cant"] = $this->mDb->getOne( $query_cant, $bindVars );
		$pParamHash["listInfo"]["count"] = $pParamHash["cant"];

		LibertyContent::postGetList( $pParamHash );
		return $ret;
	}

	/**
	* Returns titles of the contact type table
	*
	* @return array List of contact type names from the contact mamager in alphabetical order
	*/
	function getContactGroupList() {
		global $gBitUser, $gBitSmarty;

		$roles = array_keys($gBitUser->mRoles);
		$bindVars = array();
		$bindVars = array_merge( $bindVars, $roles, array( $gBitUser->mUserId ) );

		$query = "SELECT g.* FROM `".BIT_DB_PREFIX."contact_xref_type` g
				  LEFT OUTER JOIN `".BIT_DB_PREFIX."users_roles_map` purm ON ( purm.`user_id`=".$gBitUser->mUserId." ) AND ( purm.`role_id`=g.`role_id` )
				  WHERE g.`xref_type` > 0 AND (g.`role_id` IN(". implode(',', array_fill(0, count($roles), '?')) ." ) OR purm.`user_id`=?)
				  ORDER BY g.`xref_type`";
		$result = $this->mDb->query( $query, $bindVars );
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}

	/**
	* Returns titles of the contact type table
	*
	* @return array List of contact type names from the contact mamager in alphabetical order
	*/
	function getContactSourceList() {
		global $gBitUser, $gBitSmarty;

		$roles = array_keys($gBitUser->mRoles);
		$bindVars = array();
		$bindVars = array_merge( $bindVars, $roles, array( $gBitUser->mUserId ) );

		$query = "SELECT g.`cross_ref_title` AS `type_name`, g.`source` FROM `".BIT_DB_PREFIX."contact_xref_source` g
				  LEFT OUTER JOIN `".BIT_DB_PREFIX."users_roles_map` purm ON ( purm.`user_id`=".$gBitUser->mUserId." ) AND ( purm.`role_id`=g.`role_id` )
				  WHERE g.`xref_type` = 0 AND (g.`role_id` IN(". implode(',', array_fill(0, count($roles), '?')) ." ) OR purm.`user_id`=?)
				  ORDER BY g.`source`";
		$result = $this->mDb->query( $query, $bindVars );
		$ret = array();
		$cnt = 0;
		while ($res = $result->fetchRow()) {
			$ret[$cnt]['source'] = $res["source"];
			$ret[$cnt++]['name'] = trim($res["type_name"]);
		}
		return $ret;
	}

	/**
	* Returns titles of the xref type table
	* @param $xrefGroup selects a single group of xref types
	* @return array List of xref type names from the contact mamager in alphabetical order
	*/
	function getXrefTypeList( $xrefGroup = 0, $xrefTemplate = NULL ) {
		if ( $xrefTemplate ) {
			$query = "SELECT s.`cross_ref_title` AS `type_name`, s.`source`, s.`template`  FROM `".BIT_DB_PREFIX."contact_xref_source` s
					  WHERE s.`template` = '$xrefTemplate'
					  ORDER BY s.`cross_ref_title`";
			$result = $this->mDb->query($query, array( $this->mContentId, $xrefGroup ) );
		} elseif ( $xrefGroup > -1 ) {
			$query = "SELECT s.`cross_ref_title` AS `type_name`, s.`source`, s.`template`  FROM `".BIT_DB_PREFIX."contact_xref_source` s
					  LEFT JOIN `".BIT_DB_PREFIX."contact_xref` x ON x.`source` = s.`source` AND x.`content_id` = ? AND ( x.`end_date` IS NULL OR x.`end_date` > CURRENT_TIMESTAMP ) 
					  WHERE s.`xref_type` = ? AND ( x.`xref_id` IS NULL OR x.`xorder` > 0 )
					  ORDER BY s.`cross_ref_title`";
			$result = $this->mDb->query($query, array( $this->mContentId, $xrefGroup ) );
		} else {
			$query = "SELECT s.`cross_ref_title` AS `type_name`, s.`source`, s.`template`  FROM `".BIT_DB_PREFIX."contact_xref_source` s
					  LEFT JOIN `".BIT_DB_PREFIX."contact_xref` x ON x.`source` = s.`source` AND x.`content_id` = ? AND ( x.`end_date` IS NULL OR x.`end_date` > CURRENT_TIMESTAMP )
					  WHERE s.`xref_type` > 0 AND ( x.`xref_id` IS NULL OR x.`xorder` > 0 )
					  ORDER BY s.`cross_ref_title`";
			$result = $this->mDb->query($query, array( $this->mContentId ) );
		}
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret['list'][$res["source"]] = trim($res["type_name"]);
			$ret['type'][$res["source"]] = trim($res["template"]) <> '' ? trim($res["template"]) : 'generic';
		}
		return $ret;
	}

	/**
	* Returns titles of the contact format templates table
	*
	* @return array List of contact format templates names from the contact mamager in alphabetical order
	*/
	function getXrefFormatList() {
		global $gBitUser, $gBitSmarty;

		$roles = array_keys($gBitUser->mRoles);
		$bindVars = array();
		$bindVars = array_merge( $bindVars, $roles, array( $gBitUser->mUserId ) );

		$query = "SELECT DISTINCT g.`template` FROM `".BIT_DB_PREFIX."contact_xref_source` g
				  LEFT OUTER JOIN `".BIT_DB_PREFIX."users_roles_map` purm ON ( purm.`user_id`=".$gBitUser->mUserId." ) AND ( purm.`role_id`=g.`role_id` )
				  WHERE (g.`role_id` IN(". implode(',', array_fill(0, count($roles), '?')) ." ) OR purm.`user_id`=?)
				  ORDER BY g.`template`";
		$result = $this->mDb->query( $query, $bindVars );
		$ret = array();
		$cnt = 0;
		while ($res = $result->fetchRow()) {
			$ret[] = trim($res["template"]) <> '' ? trim($res["template"]) : 'generic';
		}
		return $ret;
	}

	/**
	* Returns Contract Numbers list
	* @param $contract selects a single type of contract to list
	* @return array List of contact numbers in contract number order
	*/
	function getContractList( $contract = 0 ) {
		$query = "SELECT r.`xkey_ext` AS `contract`, r.`content_id`, lc.`title`, r.`xref`, r.`xkey`, r.`data`,
				ap.*, xhC.`xkey_ext` AS house, r.xref,
				CASE WHEN r.`xkey_ext` STARTING 'C' THEN CAST ( SUBSTRING ( r.`xkey_ext` FROM 2 FOR 4 ) AS INTEGER )
				ELSE CAST ( r.`xkey_ext` AS INTEGER ) END AS XORDERBY
				FROM `".BIT_DB_PREFIX."contact_xref` r
				LEFT JOIN `".BIT_DB_PREFIX."liberty_content` lc ON lc.`content_id` = r.`content_id` 
				LEFT JOIN `".BIT_DB_PREFIX."contact_xref` xhC ON xhC.`content_id` = lc.`content_id` AND xhC.`source` = '#C' AND ( xhC.`end_date` IS NULL OR xhC.`end_date` > CURRENT_TIMESTAMP )
				LEFT JOIN `".BIT_DB_PREFIX."address_postcode` ap ON ap.`postcode` = xhC.`xkey`
				WHERE r.`source` = 'KEY_S' AND r.`xref` = ? AND ( r.`end_date` IS NULL OR r.`end_date` > CURRENT_TIMESTAMP )
				ORDER BY r.`xref`, XORDERBY";
		$result = $this->mDb->query($query, array( $contract ) );
		
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}

	/**
	 * getXrefList( &$pParamHash );
	 * Get list of xref records for this contact record
	 */
	function getContactTypes() {
		return $this->mTypes->mContactType;
	}
	
	/**
	 * getXrefList( &$pParamHash );
	 * Get list of xref records for this contact record
	 */
	function loadContentTypeList() {
		if( $this->isValid() && empty( $this->mInfo['contact_types'] ) ) {
			global $gBitUser;
		
			$roles = array_keys($gBitUser->mRoles);
			$bindVars = array();
			array_push( $bindVars, $this->mContentId );
			$bindVars = array_merge( $bindVars, $roles, array( $gBitUser->mUserId ) );

			$sql = "SELECT r.`source`, r.`cross_ref_title`, d.`content_id`
					FROM `".BIT_DB_PREFIX."contact_xref_source` r
					LEFT JOIN `".BIT_DB_PREFIX."contact_xref` d ON d.`content_id` = ? AND d.`source` = r.`source` 
					LEFT OUTER JOIN `".BIT_DB_PREFIX."users_roles_map` purm ON ( purm.`user_id`=".$gBitUser->mUserId." ) AND ( purm.`role_id`=r.`role_id` )
					WHERE r.xref_type = 0 AND (r.`role_id` IN(". implode(',', array_fill(0, count($roles), '?')) ." ) OR purm.`user_id`=?)
					ORDER BY r.`source`";
					
			$result = $this->mDb->query( $sql, $bindVars );

			while( $res = $result->fetchRow() ) {
				$this->mInfo['contact_types'][] = $res;
			}
		}
	}

	/**
	 * getXrefList( &$pParamHash );
	 * Get list of xref records for this contact record
	 */
	function loadXrefList() {
		if( $this->isValid() && empty( $this->mInfo['xref'] ) ) {
			global $gBitUser;
		
			$roles = array_keys($gBitUser->mRoles);
			$bindVars = array();
			array_push( $bindVars, $this->mDb->NOW() );
			array_push( $bindVars, $this->mContentId );
			$bindVars = array_merge( $bindVars, $roles, array( $gBitUser->mUserId ) );

			$sql = "SELECT s.xref_type, x.`xref_id`, x.`last_update_date`, x.`source`, t.`title` AS type_title,
					CASE 
					WHEN x.`end_date` < ? THEN 'history'
					ELSE t.`source` END as type_source,
					CASE
					WHEN x.`xorder` = 0 THEN s.`cross_ref_title`
					ELSE s.`cross_ref_title` || '-' || x.`xorder` END
					AS source_title,
					x.`xref`, x.`xkey`, x.`xkey_ext`, x.`data`,
					x.`start_date`, x.`end_date`
					FROM `".BIT_DB_PREFIX."contact_xref` x
					JOIN `".BIT_DB_PREFIX."contact_xref_source` s ON s.`source` = x.`source`
					JOIN `".BIT_DB_PREFIX."contact_xref_type` t ON t.`xref_type` = s.`xref_type`
					LEFT OUTER JOIN `".BIT_DB_PREFIX."users_roles_map` purm ON ( purm.`user_id`=".$gBitUser->mUserId." ) AND ( purm.`role_id`=s.`role_id` )
					WHERE x.content_id = ? AND (s.`role_id` IN(". implode(',', array_fill(0, count($roles), '?')) ." ) OR purm.`user_id`=?)
					ORDER BY x.`source`, x.`xorder`";

			$result = $this->mDb->query( $sql, $bindVars );

			while( $res = $result->fetchRow() ) {
				$this->mInfo[$res['type_source']][] = $res;
			}
		}
	}

	/**
	 * loadXref( &$pParamHash );
	 * find contact record that matches the supplied xref record
	 */
	function loadXref( $pXrefId = NULL ) {
		if( @BitBase::verifyId( $pXrefId ) ) {
			$xref = new ContactXref( $pXrefId );
			if( $xref->mContentId ) {
				$this->load( $xref->mContentId );
				$this->mInfo['xref_title'] = $xref->mContentId;
				$this->mInfo['xref_store'] = $xref->mInfo;
				$this->mInfo['format_guid'] = 'text';
			}
		}
	}

	/**
	 * storeXref( &$pParamHash );
	 * store or update xref records for this contact record
	 */
	function storeXref( &$pParamHash ) {
		$xref = new ContactXref( isset($pParamHash['xref_id']) ? $pParamHash['xref_id'] : NULL );
		if ( $xref->store( $pParamHash ) ) {
				$this->mInfo['xref_title'] = $xref->mContentId;
				$this->mInfo['xref_store'] = $xref->mInfo;
				$pParamHash['xref_id'] = $xref->mXrefId;
				$this->load();
				
				return true;
		} else return false;
	}

	/**
	 * stepXref( &$pParamHash );
	 * find contact record that matches the supplied xref record
	 */
	function stepXref( &$pParamHash ) {
		$xref = new ContactXref( $pParamHash['xref_id'] );
		if ( $xref->stepXref( $pParamHash ) ) {
			$this->mInfo['xref_title'] = $xref->mContentId;
			$this->mInfo['xref_store'] = $xref->mInfo;
			$this->load();

			return true;
		} else return false;
	}

	/**
	 * getClientList( &$pParamHash );
	 * Get list of client records for this contact record
	 * This is used to list related contact records such as contacts handled by a call center or alarm maintainer
	 */
	function loadClientList() {
		if( $this->isValid() ) {
			global $gBitUser;
		
			$roles = array_keys($gBitUser->mRoles);
			$bindVars = array();
			array_push( $bindVars, $this->mDb->NOW() );
			array_push( $bindVars, $this->mContentId );
//			$bindVars = array_merge( $bindVars, $roles, array( $gBitUser->mUserId ) );

			$sql = "SELECT r.`xref_id`, r.`content_id`, r.`last_update_date`, c.`title`,
					CASE 
					WHEN r.`end_date` < ? THEN 'history'
					ELSE r.`source` END as type_source,
					r.`xkey`, r.`xkey_ext`, r.`data`,
					r.`start_date`, r.`end_date`
					FROM `".BIT_DB_PREFIX."contact_xref` r
					JOIN `".BIT_DB_PREFIX."liberty_content` c ON c.`content_id` = r.`content_id`
					WHERE r.`xref` = ? AND r.`source` = '#A' 
					ORDER BY c.`title`";
//					LEFT OUTER JOIN `".BIT_DB_PREFIX."users_roles_map` purm ON ( purm.`user_id`=".$gBitUser->mUserId." ) AND ( purm.`role_id` = s.`role_id` )
//					AND (s.`role_id` IN(". implode(',', array_fill(0, count($roles), '?')) ." ) OR purm.`user_id`=?)

			$result = $this->mDb->query( $sql, $bindVars );

			while( $res = $result->fetchRow() ) {
				$this->mInfo['client_list'][] = $res;
			}
		}
	}

	/**
	 * getAddressList( &$pParamHash );
	 * Get list of address records for this contact record
	 */
	function loadAddressList() {
		if( $this->isValid() && empty( $this->mInfo['xref'] ) ) {
			global $gBitUser;
		
			$roles = array_keys($gBitUser->mRoles);
			$bindVars = array();
			array_push( $bindVars, $this->mDb->NOW() );
			array_push( $bindVars, $this->mContentId );
			$bindVars = array_merge( $bindVars, $roles, array( $gBitUser->mUserId ) );

			$sql = "SELECT s.xref_type, x.`xref_id`, x.`last_update_date`, x.`source`, t.`title` AS type_title,
					CASE 
					WHEN x.`end_date` < ? THEN 'history'
					ELSE t.`source` END as type_source,
					CASE
					WHEN x.`xorder` = 0 THEN s.`cross_ref_title`
					ELSE s.`cross_ref_title` || '-' || x.`xorder` END
					AS source_title,
					x.`xkey_ext` AS house, ap.`add1`, ap.`add2`, ap.`add3`, ap.`add4`, ap.`town`, ap.`county`, x.`xkey` AS postcode, ap.`grideast`, ap.`gridnorth`, x.`data`,
					x.`start_date`, x.`end_date`
					FROM `".BIT_DB_PREFIX."contact_xref` x
					JOIN `".BIT_DB_PREFIX."contact_xref_source` s ON s.`source` = x.`source`
					JOIN `".BIT_DB_PREFIX."contact_xref_type` t ON t.`xref_type` = s.`xref_type`
					LEFT JOIN `".BIT_DB_PREFIX."address_postcode` ap ON ap.`postcode` = x.`xkey`
					LEFT OUTER JOIN `".BIT_DB_PREFIX."users_roles_map` purm ON ( purm.`user_id`=".$gBitUser->mUserId." ) AND ( purm.`role_id`=s.`role_id` )
					WHERE x.content_id = ? AND s.`template` = 'address' AND (s.`role_id` IN(". implode(',', array_fill(0, count($roles), '?')) ." ) OR purm.`user_id`=?)
					ORDER BY x.`source`, x.`xorder`";

			$result = $this->mDb->query( $sql, $bindVars );

			while( $res = $result->fetchRow() ) {
				if ( $res['grideast'] and $res['grideast'] <> '00000' ) {
					$os1 = new OSRef( $res['grideast']*10, $res['gridnorth']*10 );
					$ll1 = $os1->toLatLng();
					$res['x_coordinate'] = $ll1->lng;
					$res['y_coordinate'] = $ll1->lat;
				} else {
					$res['house'] = $res['house'].' - '.$res['data'];
				}

				$this->mInfo['address'][] = $res;
			}
		}
	}

	/**
	 * ContactRecordLoad( $data );
	 * simple csv contact list import 
	 * Uncomment to enable
	 */
//   require( CONTENT_PKG_PATH.'import/ImportContact.php');

	/**
	 * SageRecordLoad( $data ); 
	 * sage csv data import 
	 * Uncomment to enable
	 */
//   require( CONTENT_PKG_PATH.'import/ImportSage.php');

	/**
	 * PhxRecordLoad( $data ); 
	 * phoenix security csv data import 
	 * Uncomment to enable
	 */
//	include( CONTENT_PKG_PATH.'import/ImportPhx2.php');

	/**
	 * wandeRecordLoad( $data ); 
	 * wande data file import 
	 * Uncomment to enable
	 */
//	include( CONTENT_PKG_PATH.'import/ImportWande.php');

}
?>
