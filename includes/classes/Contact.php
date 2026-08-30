<?php
/**
 * Contact content class — person or business contact stored in liberty_content.
 *
 * Both store their name directly in lc.title — organisation name for a business,
 * pipe-encoded prefix|forename|surname|suffix for a person (see ContactPerson,
 * whose load() normalises mInfo['title'] to a display-ready form).
 *
 * @package contact
 */
namespace Bitweaver\Contact;

use Bitweaver\BitBase;
use Bitweaver\BitDate;
use Bitweaver\Liberty\LibertyContent;		// Contact base class
require_once CONTACT_PKG_PATH.'lib/phpcoord-2.3.php';

define( 'CONTACT_CONTENT_TYPE_GUID', 'contact' );
defined( 'CONTACTPERSON_CONTENT_TYPE_GUID' )   || define( 'CONTACTPERSON_CONTENT_TYPE_GUID',   'contactperson' );
defined( 'CONTACTBUSINESS_CONTENT_TYPE_GUID' ) || define( 'CONTACTBUSINESS_CONTENT_TYPE_GUID', 'contactbusiness' );

class Contact extends LibertyContent {

	public $mParentId;
	public $mDate;
	public $mTypes;

	protected $mXrefTypeKey = 'contact_types';

	/**
	 * @param int|null $pContactId  Unused legacy param — pass null.
	 * @param int|null $pContentId  liberty_content.content_id to load.
	 */
	public function __construct( $pContactId = NULL, $pContentId = NULL ) {
		parent::__construct();
		$this->registerContentType( CONTACT_CONTENT_TYPE_GUID, [
				'content_type_guid' => CONTACT_CONTENT_TYPE_GUID,
				'content_name' => 'Contact Entry',
				'handler_class' => 'Contact',
				'handler_package' => 'contact',
				'handler_file' => 'Contact.php',
				'maintainer_url' => 'http://lsces.co.uk',
			] );
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
	}

	/**
	 * @return bool TRUE when mContentId refers to a real liberty_content row of this
	 *              object's own content type (Contact/ContactPerson/ContactBusiness all
	 *              correctly distinguished via mContentTypeGuid) — not just an id that
	 *              looks syntactically valid.
	 */
	public function isValid() {
		if( !BitBase::verifyId( $this->mContentId ) ) {
			return false;
		}
		return (bool)$this->mDb->getOne(
			"SELECT 1 FROM `".BIT_DB_PREFIX."liberty_content` WHERE `content_id` = ? AND `content_type_guid` = ?",
			[ $this->mContentId, $this->mContentTypeGuid ]
		);
	}

	/** Load this content item's type-tag markers (P01/P02/B01–B04) into mInfo. */
	public function loadXrefTypeList(): void {
		if ( !$this->isValid() || !empty( $this->mInfo[$this->mXrefTypeKey] ) ) return;
		$this->mInfo[$this->mXrefTypeKey] = $this->xrefType()->getContentTypeMarkers( $this->mContentId );
	}

	/**
	 * Return all available type-tag options for this contact's edit form.
	 *
	 * @return array[]  Each element: ['item' => string, 'name' => string]
	 */
	public function getAvailableTypeItems(): array {
		return $this->xrefType()->getTypeMarkers();
	}

	/**
	 * Load contact record, name parts, and xref groups into $this->mInfo.
	 *
	 * @param int|null   $pContentId    Override mContentId for this load.
	 * @param array|null $pPluginParams Passed through to LibertyContent::load().
	 */
	public function load( $pContentId = NULL, $pPluginParams = NULL ) {
		if ( $pContentId ) $this->mContentId = (int)$pContentId;
		if( $this->verifyId( $this->mContentId ) ) {
			$query = "select con.*, lc.*,
				uue.`login` AS modifier_user, uue.`real_name` AS modifier_real_name,
				uuc.`login` AS creator_user, uuc.`real_name` AS creator_real_name,
				uu.`login` AS linked_user_login, uu.`real_name` AS linked_user_name
				FROM `".BIT_DB_PREFIX."contact` con
				LEFT JOIN `".BIT_DB_PREFIX."liberty_content` lc ON lc.content_id = con.content_id
				LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON (uue.`user_id` = lc.`modifier_user_id`)
				LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON (uuc.`user_id` = lc.`user_id`)
				LEFT JOIN `".BIT_DB_PREFIX."users_users` uu ON uu.`user_id` = con.`role_id`
				WHERE con.`content_id`=?";
			$result = $this->mDb->query( $query, [ $this->mContentId ] );

			if ( $result && $result->numRows() ) {
				$this->mInfo = $result->fields;
				$this->mContentId = (int)$result->fields['content_id'];
				$this->mContactName = $result->fields['title'];
				$this->mInfo['creator'] = $result->fields['creator_real_name'] ?? $result->fields['creator_user'];
				$this->mInfo['editor'] = $result->fields['modifier_real_name'] ?? $result->fields['modifier_user'];
				$this->mInfo['display_url'] = $this->getDisplayUrl();
				$this->mInfo['organisation'] = trim( $this->mInfo['title'] ?? '' );

				$this->loadXrefInfo();

				if ( $imgXref = $this->mXrefInfo->findRowByItem( 'IMG' ) ) {
					$this->mInfo['client_gallery'] = $imgXref['xkey'];
				}
				if ( $addressXref = $this->mXrefInfo->findRowByItem( '#S' ) ) {
					$this->mInfo['house'] = $addressXref['xkey_ext'];
					if ( !empty( $addressXref['xkey'] ) ) {
						$postcodeRow = $this->mDb->getRow(
							"SELECT * FROM `".BIT_DB_PREFIX."address_postcode` WHERE `postcode` = ?",
							[ $addressXref['xkey'] ]
						);
						if ( $postcodeRow ) {
							$this->mInfo = array_merge( $this->mInfo, $postcodeRow );
						}
					}
				}
				if ( $linkedXref = $this->mXrefInfo->findRowByItem( '#L' ) ) {
					$this->mInfo['x_coordinate'] = $linkedXref['xkey'];
					$this->mInfo['y_coordinate'] = $linkedXref['xkey_ext'];
				}

				if ( empty( $this->mInfo['x_coordinate'] ) && !empty( $this->mInfo['postcode'] ) && ( $this->mInfo['grideast'] ?? '00000' ) <> '00000' ) {
					$os1 = new \OSRef( $this->mInfo['grideast']*10, $this->mInfo['gridnorth']*10 );
					$ll1 = $os1->toLatLng();
					$this->mInfo['y_coordinate'] = $ll1->lat;
					$this->mInfo['x_coordinate'] = $ll1->lng;
				}

				$this->loadXrefTypeList();
			}
		}
		LibertyContent::load();

	}

	/**
	 * Validate and normalise $pParamHash before storing.
	 *
	 * Builds lc.title from organisation (business) — ContactPerson overrides this
	 * to build title from name parts instead, the same way, before delegating here.
	 *
	 * @param  array $pParamHash  Data to store; modified in place.
	 * @return bool  TRUE if valid; FALSE with $this->mErrors set on failure.
	 */
	public function verify( &$pParamHash ): bool {
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

		if( empty( $pParamHash['title'] ) ) {
			$pParamHash['title'] = $pParamHash['organisation'] ?? '';
		}
		$pParamHash['title'] = trim( $pParamHash['title'] );
		$pParamHash['contact_store']['xkey'] = $pParamHash['xkey'];
		if( array_key_exists( 'user_id', $pParamHash ) ) {
			$pParamHash['contact_store']['role_id'] = $pParamHash['user_id'] ? (int)$pParamHash['user_id'] : null;
		}
		return count( $this->mErrors ) == 0;
	}

	/**
	 * Persist contact and its type xrefs inside a transaction.
	 *
	 * Calls verify() then LibertyContent::store(). On a new record also
	 * inserts the contact_address stub row. Writes contact_types xrefs
	 * (including $00 person-name and $01 organisation) if present in hash.
	 *
	 * @param  array $pParamHash  Data to persist; modified in place.
	 * @return bool  TRUE on success; FALSE with $this->mErrors set on failure.
	 */
	public function store( &$pParamHash ): bool {
		if( $this->verify( $pParamHash ) ) {

			// Start a transaction wrapping the whole insert into liberty

			$this->mDb->StartTrans();
			if ( LibertyContent::store( $pParamHash ) ) {
				$table = BIT_DB_PREFIX."contact";
				$atable = BIT_DB_PREFIX."contact_address";

				// mContentId will not be set until the secondary data has commited
				if( !empty( $pParamHash['contact_store']['content_id'] ) ) {
					$result = $this->mDb->associateUpdate( $table, $pParamHash['contact_store'], [ "content_id" => $this->mContentId ] );
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
				// fContactTypesSubmitted (a hidden field alongside the checkboxes, see
				// edit_type_header.tpl) marks that this section was genuinely on the form -
				// an all-unchecked checkbox group sends no contact_types[] key at all, which
				// is otherwise indistinguishable from a caller (an import script, say) that
				// doesn't touch type tags at all and must leave existing ones alone. Confirmed
				// live 2026-08-29 via xdebug: the old `!empty($pParamHash['contact_types'])`
				// guard meant unchecking someone's only type tag silently did nothing at all.
				if( !empty( $pParamHash['fContactTypesSubmitted'] ) ) {
					$wantedTypes = $pParamHash['contact_types'] ?? [];

					// Type markers (sort_order=0) are excluded from mXrefInfo by design, so
					// read what's actually stored via getTypeMarkerXrefs() instead. P01 needs
					// no special-casing any more - it's a normal toggleable type like the rest.
					// Diff against existing rather than delete-all-then-reinsert, so an
					// unrelated add/remove doesn't reset an unchanged tag's entry_date.
					$existingTypeXrefs = $this->xrefType()->getTypeMarkerXrefs( $this->mContentId );
					$existingItems = array_keys( $existingTypeXrefs );
					foreach ( $existingTypeXrefs as $item => $xrefId ) {
						if ( !in_array( $item, $wantedTypes, true ) ) {
							$stepHash = [ 'xref_id' => $xrefId, 'expunge' => 3 ];
							$this->stepXref( $stepHash );
						}
					}
					foreach ( array_diff( $wantedTypes, $existingItems ) as $addedItem ) {
						$xrefHash = [
							'content_id' => $this->mContentId,
							'item'       => $addedItem,
							'fAddXref'   => 1,
						];
						$this->storeXref( $xrefHash );
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
		return count( $this->mErrors ) == 0;
	}

	/**
	 * Delete this contact. Xref cleanup is LibertyContent::expunge()'s own job,
	 * not this method's — it already deletes every xref row for this content_id.
	 *
	 * @return bool TRUE on success; FALSE if the contact is not valid or the delete fails.
	 */
	public function expunge(): bool
	{
		return $this->isValid() && LibertyContent::expunge();
	}

	/** @return bool Always TRUE — contacts support comments. */
	public function isCommentable(){
		global $gBitSystem;
		return TRUE; // $gBitSystem->isFeatureActive( 'contact_post_comments' );
	}

	/**
	 * @param  int|null $pContentId  Defaults to $this->mContentId.
	 * @return string   URL to view.php for this contact.
	 */
	public function getDisplayUrl( $pContentId=NULL ) {
		global $gBitSystem;
		if( empty( $pContentId ) ) {
			$pContentId = $this->mContentId;
		}

		return CONTACT_PKG_URL.'view.php?content_id='.$pContentId;
	}

	/**
	 * @param  string|null $pLinkText  Unused — link text is derived from mInfo.
	 * @param  array|null  $pMixed     mInfo-style hash; must contain content_id and title.
	 * @param  string|null $pAnchor    Unused.
	 * @return string      HTML anchor element.
	 */
	public function getDisplayLink( $pLinkText=NULL, $pMixed=NULL, $pAnchor=NULL ) {
		if ( $this->mContentId != $pMixed['content_id'] ) $this->load($pMixed['content_id']);

		$ret = ( empty( $this->mInfo['content_id'] ) )
			? '<a href="' . $this->getDisplayUrl( $pMixed['content_id'] ) . '">' . $pMixed['title'] . '</a>'
			: '<a href="' . $this->getDisplayUrl( $pMixed['content_id'] ) . '">' . "Contact - " . $this->mInfo['title'] . '</a>';
		return $ret;
	}

	/**
	 * @param  array|null $pHash     mInfo-style hash; defaults to $this->mInfo.
	 * @param  bool       $pDefault  Unused; kept for LibertyContent interface compatibility.
	 * @return string|null           Prefixed title ("Contact - <name>") or null if empty.
	 */
	public function getTitle( $pHash = NULL, $pDefault=TRUE ) {
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
	 * Return a paged list of contacts matching filter criteria.
	 *
	 * Recognised keys in $pParamHash: user_id (filters by linked user; stored in con.role_id), find_xref,
	 * find_title, active, sort_mode, max_records, offset.
	 * Sets $pParamHash['cant'] and $pParamHash['listInfo'] on return.
	 *
	 * find_location/find_postcode aren't handled here — they depended on
	 * address_postcode, which no real site populates any more (postcode lookup is
	 * moving to mapper/OSM instead). The search UI still shows those fields; they
	 * currently do nothing.
	 *
	 * TODO: "filter/search a content list by an xref attribute" needs one proper,
	 * generic mechanism — not another one-off patch per attribute. find_xref below
	 * (unscoped LIKE on any item's xkey), the removed type-tag filter (P01/B0x),
	 * find_location/find_postcode's future postcode/address replacement, and
	 * export_contacts.php's hand-rolled per-item subqueries are all the same
	 * underlying gap, not four separate ones. The lookup*() family on
	 * LibertyContent (lookupTitles/lookupByXref/lookupXrefByTemplate) covers
	 * finding a single value; this would be its list-filtering sibling.
	 *
	 * @param  array $pParamHash  Filter and pagination params; modified in place.
	 * @return array              Flat array of result row hashes.
	 */
	public function getList( &$pParamHash ) {
		global $gBitSystem, $gBitUser;

		LibertyContent::prepGetList( $pParamHash );

		$selectSql = '';
		$joinSql = '';
		$whereSql = '';
		$bindVars = [];

		if ( isset( $pParamHash['user_id'] ) ) {
			array_push( $bindVars, $this->mContentTypeGuid );
			if ( $pParamHash['user_id'] > 0 ) {
				$whereSql .= " AND con.`role_id` = ? ";
				$bindVars[] = (int)$pParamHash['user_id'];
			}
		}

		// this will set $sort_mode, $max_records and $offset
		extract( $pParamHash );

		// Unscoped LIKE on any item's xkey — see getList()'s own TODO.
		if( isset( $find_xref ) and is_string( $find_xref ) and $find_xref <> '' ) {
			$joinSql .= "JOIN `".BIT_DB_PREFIX."liberty_xref` cy ON cy.`content_id` = con.`content_id` AND cy.`xkey` like ? ";
			$bindVars[] = '%' . strtoupper( $find_xref ). '%';
			$pParamHash["listInfo"]["ihash"]["find_xref"] = $find_xref;
		}

		if ( !isset( $pParamHash['user_id'] ) ) {
			array_push( $bindVars, $this->mContentTypeGuid );
		}

		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars, NULL, $pParamHash );

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


		if( isset( $find_title ) and is_string( $find_title ) and $find_title <> '' ) {
			$whereSql .= " AND UPPER( lc.`title` ) like ? ";
			$bindVars[] = '%' . strtoupper( $find_title ). '%';
			$pParamHash["listInfo"]["ihash"]["find_title"] = $find_title;
		}

		$query = "SELECT con.`content_id` as content_id, con.*, lc.*
			FROM `".BIT_DB_PREFIX."contact` con
			LEFT JOIN `".BIT_DB_PREFIX."liberty_content` lc ON lc.`content_id` = con.`content_id`
			$joinSql
			WHERE lc.`content_type_guid` = ? $whereSql
			ORDER BY ".$this->mDb->convertSortmode( $sort_mode );

		$query_cant = "SELECT COUNT( * )
			FROM `".BIT_DB_PREFIX."contact` con
			LEFT JOIN `".BIT_DB_PREFIX."liberty_content` lc ON lc.content_id = con.content_id
			$joinSql WHERE lc.`content_type_guid` = ? $whereSql ";
		$result = $this->mDb->query( $query, $bindVars, $max_records, $offset );

		$ret = [];
		while( $res = $result->fetchRow() ) {
			// Per-row xref lookup rather than a JOIN in the main query — cheap at
			// list-page scale (max_records already caps this), and lets further
			// enrichment fields be added here the same way instead of growing the
			// query itself. See getList()'s own docblock re: the postcode/address
			// rebuild this is standing in for.
			$address = LibertyContent::lookupXrefByTemplate( $res['content_id'], 'address', 'contact' );
			$res['house'] = $address['xkey_ext'] ?? null;
			$res['refs'] = LibertyContent::countXrefEntries( $res['content_id'], $this->mContentTypeGuid, $this->mPackageGuid );
			$ret[] = $res;
		}
		$pParamHash["cant"] = $this->mDb->getOne( $query_cant, $bindVars );
		$pParamHash["listInfo"]["count"] = $pParamHash["cant"];

		LibertyContent::postGetList( $pParamHash );
		return $ret;
	}
}