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
namespace Bitweaver\Contact;
use Bitweaver\BitBase;

/**
 * @package contact
 */
class ContactType extends BitBase {
	public $mContactType;

	public function __construct() {
		parent::__construct();
	}

	/**
	 * setup()
	 * Setup the contact types for use in the content filter.
	 */
	public function setup() {
		global $gBitUser, $gBitSmarty;

			$roles = array_keys($gBitUser->mRoles);
			$bindVars = [];
			$bindVars = array_merge( $bindVars, $roles, array( $gBitUser->mUserId ) );

			$sql = "SELECT r.`source`, r.`cross_ref_title`
					FROM `".BIT_DB_PREFIX."contact_xref_source` r
					LEFT OUTER JOIN `".BIT_DB_PREFIX."users_roles_map` purm ON ( purm.`user_id`=".$gBitUser->mUserId." ) AND ( purm.`role_id`=r.`role_id` )
					WHERE r.xref_type = 0 AND (r.`role_id` IN(". implode(',', array_fill(0, count($roles), '?')) ." ) OR purm.`user_id`=?)
					ORDER BY r.`source`";

		$result = $this->mDb->query( $sql, $bindVars );

		while( $res = $result->fetchRow() ) {
			$this->mContactType[ $res['source']] = $res['cross_ref_title'];
		}

//		asort($this->mContactType);
		$gBitSmarty->assign( 'contContactTypes', $this->mContactType );
	}

	/**
	 * processRequestHash(&$pRequest, &$pStore)
	 * Build contact_type settins hash for the session
	 */
	public function processRequestHash(&$pRequest, &$pStore) {
		global $gBitUser;
		if( !empty( $pRequest["contact_type_guid"] ) ) {
			if( $gBitUser->isRegistered() ) {
				$gBitUser->storePreference( 'contact_default_guids', serialize( $pRequest['contact_type_guid'] ) );
			}
			$pStore['contact_type_guid'] = $pRequest["contact_type_guid"];
		} elseif( !isset( $pStore['content_type_guid'] ) && $gBitUser->getPreference( 'contact_default_guids' ) && $gBitUser->isRegistered() ) {
			$pStore['contact_type_guid'] = unserialize( $gBitUser->getPreference( 'contact_default_guids' ) );
		} elseif( !isset( $pStore['content_type_guid'] ) ) {
			$pStore['contact_type_guid'] = [];
		} elseif( isset( $pRequest["refresh"] ) && !isset( $pRequest["contact_type_guid"] ) ) {
			$pStore['contact_type_guid'] = [];
		}
	}

	public static function getContactTypeList( $pOptionHash=NULL ) {
		global $gBitSystem;

		$where = '';
		$bindVars = [];
		if( !empty( $pOptionHash['active_role'] ) ) {
			$where = " WHERE cxt.`role_id` = ? ";
			$bindVars[] = $pOptionHash['active_role'];
		}
		if ( !empty(  $pOptionHash['title'] ) ) {
			$where = " WHERE cxt.`title` = ? ";
			$bindVars[] = $pOptionHash['title'];
		}

		$query = "SELECT cxt.*
				 FROM `".BIT_DB_PREFIX."contact_xref_type` cxt
				 $where ORDER BY cxt.`xref_type`";

		$result = $gBitSystem->mDb->query( $query, $bindVars );

        $ret = [];

        while( $res = $result->fetchRow() ) {
			$res["num_types"] = $gBitSystem->mDb->getOne( "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."contact_xref_source` WHERE `xref_type`= ?", array( $res["xref_type"] ) );

            $ret[] = $res;
        }

        return $ret;
    }
}
