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
class ContactType extends BitBase {
	var $mContactType;

	function ContactType() {
		parent::__construct();
	}

	/**
	 * setup()
	 * Setup the contact types for use in the content filter.
	 */
	function setup() {
		global $gBitUser, $gBitSmarty;

			$roles = array_keys($gBitUser->mRoles);
			$bindVars = array();
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
		$gBitSmarty->assign_by_ref( 'contContactTypes', $this->mContactType );
	}

	/**
	 * processRequestHash(&$pRequest, &$pStore)
	 * Build contact_type settins hash for the session
	 */
	function processRequestHash(&$pRequest, &$pStore) {
		global $gBitUser;
		if( !empty( $pRequest["contact_type_guid"] ) ) {
			if( $gBitUser->isRegistered() ) {
				$gBitUser->storePreference( 'contact_default_guids', serialize( $pRequest['contact_type_guid'] ) );
			}
			$pStore['contact_type_guid'] = $pRequest["contact_type_guid"];
		} elseif( !isset( $pStore['content_type_guid'] ) && $gBitUser->getPreference( 'contact_default_guids' ) && $gBitUser->isRegistered() ) {
			$pStore['contact_type_guid'] = unserialize( $gBitUser->getPreference( 'contact_default_guids' ) );
		} elseif( !isset( $pStore['content_type_guid'] ) ) {
			$pStore['contact_type_guid'] = array();
		} elseif( isset( $pRequest["refresh"] ) && !isset( $pRequest["contact_type_guid"] ) ) {
			$pStore['contact_type_guid'] = array();
		}
	}

}
?>
