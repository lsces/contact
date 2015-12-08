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

	function __construct() {
		parent::__construct();

	}

	public static function getContactXrefTypeList( $pOptionHash=NULL ) {
		global $gBitSystem;

		$where = '';
		$bindVars = array();
		if( !empty( $pOptionHash['active_role'] ) ) {
			$where = " WHERE cxs.`role_id` = ? ";
			$bindVars[] = $pOptionHash['active_role'];
		}
		if ( !empty(  $pOptionHash['source'] ) ) {
			$where = " WHERE cxs.`source` = ? ";
			$bindVars[] = $pOptionHash['source'];
		}

		$query = "SELECT cxs.*
				 FROM `".BIT_DB_PREFIX."contact_xref_source` cxs
				 $where ORDER BY cxs.`xref_type`, cxs.source`";

		$result = $gBitSystem->mDb->query( $query, $bindVars );

        $ret = array();

        while( $res = $result->fetchRow() ) {
			$res["num_entries"] = $gBitSystem->mDb->getOne( "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."contact_xref` WHERE `source`= ?", array( $res["source"] ) );

            $ret[] = $res;
        }

        return $ret;
    }

}
?>
