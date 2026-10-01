<?php
/**
 * @package contact
 */

global $gBitInstaller;

$X = BIT_DB_PREFIX;

$gBitInstaller->registerPackageUpgrade(
	[
		'package'     => CONTACT_PKG_NAME,
		'version'     => '5.0.2',
		'description' => 'Migrate legacy package-level $00-$05 contact type tags to the current per-type '
			.'format. Before contact\'s xref moved into liberty, one contact record held both a person '
			.'name ($00, "prefix|forename|surname|suffix") and an organisation ($01), and sites tagged '
			.'business types with $01-$05 at package level. $01-$05 rows on businesses become B01-B05 '
			.'(their values - e.g. domain names - kept). $00 did two jobs on one record - the "has a '
			.'named contact" tag and that contact\'s name - so it becomes the B03 Contact tag, its name '
			.'moving to NAME on the business. No records are created or converted. A site with no $xx '
			.'rows is untouched. Apply the site\'s local '
			.'contact scheme (B0x/NAME definitions) via admin_local_scheme.php BEFORE running this.',
		'post_upgrade' => 'Check list_contacts: businesses should carry B0x type tags, and each former '
			.'$00 business should show its Contact Name under General Contact Details.',
	],
	[
		// A pre-liberty install's contact_address.last_update_date can carry adodb's install-time
		// literal ('2010-10-18, 16:23:00' - not even valid TIMESTAMP syntax) as its DEFAULT instead of
		// LOCALTIMESTAMP, failing every new address stub row (and so every new contact). Harmless where
		// it's already right.
		[ 'QUERY' => [
			'SQL92' => [
				"ALTER TABLE `{$X}contact_address` ALTER COLUMN `last_update_date` SET DEFAULT LOCALTIMESTAMP",
			],
		]],

		// $01-$05 -> B01-B05 on businesses, value (xkey_ext) untouched.
		[ 'QUERY' => [
			'SQL92' => [
				"UPDATE `{$X}liberty_xref` SET `item` = 'B' || SUBSTRING( `item` FROM 2 )
				 WHERE `item` IN ( '\$01', '\$02', '\$03', '\$04', '\$05' )
				 AND `content_id` IN ( SELECT `content_id` FROM `{$X}liberty_content` WHERE `content_type_guid` = 'contactbusiness' )",
			],
		]],

		// $00 did two jobs on one legacy record: marking it as having a named contact, and holding
		// that contact's name ("prefix|forename|surname|suffix"). Today's model: the record stays a
		// business, the tag becomes B03 Contact, and the name moves to NAME on the business - display
		// form in xkey_ext (what the xref grid shows), the parts as JSON in data (same shape a
		// person's NAME uses). A record that already has a B03 just loses the redundant $00 tag.
		[ 'PHP' => '
			$rows = $this->mDb->getAll(
				"SELECT x.`xref_id`, x.`content_id`, x.`xkey_ext` FROM `".BIT_DB_PREFIX."liberty_xref` x
				 JOIN `".BIT_DB_PREFIX."liberty_content` lc ON lc.`content_id` = x.`content_id`
				 WHERE x.`item` = ? AND x.`end_date` IS NULL AND lc.`content_type_guid` = ?",
				[ "\$00", "contactbusiness" ]
			);
			foreach( $rows as $row ) {
				$contentId = (int)$row["content_id"];
				$parts = explode( "|", (string)$row["xkey_ext"] );
				if( count( $parts ) >= 4 ) {
					$name = array_map( "trim", array_slice( $parts, 0, 4 ) );
				} else {
					$name = [ "", trim( $parts[0] ?? "" ), trim( $parts[1] ?? "" ), "" ];
				}
				$display = trim( implode( " ", array_filter( $name, "strlen" ) ) );
				if( $display !== "" ) {
					\Bitweaver\Liberty\LibertyContent::upsertXrefByContentId( $contentId, "NAME", [ "xkey_ext" => $display, "edit" => json_encode( $name ) ] );
				}
				$hasB03 = $this->mDb->getOne(
					"SELECT COUNT(*) FROM `".BIT_DB_PREFIX."liberty_xref` WHERE `content_id` = ? AND `item` = ? AND `end_date` IS NULL",
					[ $contentId, "B03" ]
				);
				if( $hasB03 ) {
					$this->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."liberty_xref` WHERE `xref_id` = ?", [ (int)$row["xref_id"] ] );
				} else {
					$this->mDb->query(
						"UPDATE `".BIT_DB_PREFIX."liberty_xref` SET `item` = ?, `xkey_ext` = NULL WHERE `xref_id` = ?",
						[ "B03", (int)$row["xref_id"] ]
					);
				}
			}
		'],

		// Retire the package-level $xx item definitions nothing references any more. The
		// package-level 'type' group itself is left alone: other sites (merg) rely on it.
		[ 'QUERY' => [
			'SQL92' => [
				"DELETE FROM `{$X}liberty_xref_item` WHERE `content_type_guid` = 'contact'
				 AND `item` IN ( '\$00', '\$01', '\$02', '\$03', '\$04', '\$05' )
				 AND NOT EXISTS ( SELECT 1 FROM `{$X}liberty_xref` x WHERE x.`item` = `{$X}liberty_xref_item`.`item` )",
			],
		]],
	]
);
