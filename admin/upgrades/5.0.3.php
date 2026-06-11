<?php
/**
 * 5.0.3 — Introduce ContactPerson and ContactBusiness subclasses.
 *
 * Steps:
 *  1. Insert liberty_content_types rows for contactperson and contactbusiness.
 *  2. Insert liberty_xref_group 'type' rows at contactperson and contactbusiness level.
 *  3. Insert xref_item rows with new P01/P02 and B01-B04 codes.
 *  4. Delete the now-superseded 'type' group and $0x items from content_type_guid='contact'.
 *  5. Migrate liberty_content: records with a $00 xref → contactperson; remainder → contactbusiness.
 *  6. Rename existing liberty_xref type-tag rows from $0x to P0x/B0x.
 *
 * @package contact
 */

global $gBitInstaller;

$X = BIT_DB_PREFIX;

$gBitInstaller->registerPackageUpgrade(
	[
		'package'     => 'contact',
		'version'     => '5.0.3',
		'description' => 'Introduce ContactPerson and ContactBusiness content type subclasses.',
	],
	[
		// --- Step 1: register content types ---
		[ 'QUERY' => [ 'SQL92' => [
			"UPDATE OR INSERT INTO `{$X}liberty_content_types`
				(`content_type_guid`,`content_name`,`content_name_plural`,`handler_class`,`handler_package`,`handler_file`,`maintainer_url`)
			 VALUES ('contactperson','Person Contact','Person Contacts','ContactPerson','contact','ContactPerson.php','http://lsces.co.uk')
			 MATCHING (`content_type_guid`)",
			"UPDATE OR INSERT INTO `{$X}liberty_content_types`
				(`content_type_guid`,`content_name`,`content_name_plural`,`handler_class`,`handler_package`,`handler_file`,`maintainer_url`)
			 VALUES ('contactbusiness','Business Contact','Business Contacts','ContactBusiness','contact','ContactBusiness.php','http://lsces.co.uk')
			 MATCHING (`content_type_guid`)",
		]]],

		// --- Step 2: xref_group 'type' rows ---
		[ 'QUERY' => [ 'SQL92' => [
			"UPDATE OR INSERT INTO `{$X}liberty_xref_group`
				(`x_group`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`)
			 VALUES ('type','contactperson','Person Type',0,3,'')
			 MATCHING (`x_group`,`content_type_guid`)",
			"UPDATE OR INSERT INTO `{$X}liberty_xref_group`
				(`x_group`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`)
			 VALUES ('type','contactbusiness','Business Type List',0,3,'')
			 MATCHING (`x_group`,`content_type_guid`)",
		]]],

		// --- Step 3: xref_item rows with new codes ---
		[ 'QUERY' => [ 'SQL92' => [
			// person types
			"UPDATE OR INSERT INTO `{$X}liberty_xref_item`
				(`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`)
			 VALUES ('P01','contactperson','type','Personal',0,3,'',NULL)
			 MATCHING (`item`,`content_type_guid`)",
			"UPDATE OR INSERT INTO `{$X}liberty_xref_item`
				(`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`)
			 VALUES ('P02','contactperson','type','MERG Kit Elf',0,3,'',NULL)
			 MATCHING (`item`,`content_type_guid`)",
			// business subtypes
			"UPDATE OR INSERT INTO `{$X}liberty_xref_item`
				(`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`)
			 VALUES ('B01','contactbusiness','type','Service',0,3,'',NULL)
			 MATCHING (`item`,`content_type_guid`)",
			"UPDATE OR INSERT INTO `{$X}liberty_xref_item`
				(`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`)
			 VALUES ('B02','contactbusiness','type','Manufacturer',0,3,'',NULL)
			 MATCHING (`item`,`content_type_guid`)",
			"UPDATE OR INSERT INTO `{$X}liberty_xref_item`
				(`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`)
			 VALUES ('B03','contactbusiness','type','Distributor',0,3,'',NULL)
			 MATCHING (`item`,`content_type_guid`)",
			"UPDATE OR INSERT INTO `{$X}liberty_xref_item`
				(`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`)
			 VALUES ('B04','contactbusiness','type','Supplier',0,3,'',NULL)
			 MATCHING (`item`,`content_type_guid`)",
		]]],

		// --- Step 4: remove old 'type' group and $0x items from contact ---
		[ 'QUERY' => [ 'SQL92' => [
			"DELETE FROM `{$X}liberty_xref_item` WHERE `content_type_guid` = 'contact' AND `x_group` = 'type'",
			"DELETE FROM `{$X}liberty_xref_group` WHERE `content_type_guid` = 'contact' AND `x_group` = 'type'",
		]]],

		// --- Step 5: migrate liberty_content records ---
		// Person detection uses old $00 item (still present in liberty_xref at this point)
		[ 'QUERY' => [ 'SQL92' => [
			"UPDATE `{$X}liberty_content` SET `content_type_guid` = 'contactperson'
			 WHERE `content_type_guid` = 'contact'
			   AND `content_id` IN (
				   SELECT `content_id` FROM `{$X}liberty_xref` WHERE `item` = '\$00'
			   )",
			"UPDATE `{$X}liberty_content` SET `content_type_guid` = 'contactbusiness'
			 WHERE `content_type_guid` = 'contact'",
		]]],

		// --- Step 6: rename existing liberty_xref type-tag rows ---
		// $00 (Personal name tag) → P01; $05 (Kit Elf) → P02
		// $02 (Manufacturer) → B02; $03 (Distributor) → B03; $04 (Supplier) → B04
		// $01 (deprecated Business) — deleted; B01 (Service) is new, no existing data
		[ 'QUERY' => [ 'SQL92' => [
			"UPDATE `{$X}liberty_xref` SET `item` = 'P01' WHERE `item` = '\$00'",
			"UPDATE `{$X}liberty_xref` SET `item` = 'P02' WHERE `item` = '\$05'",
			"UPDATE `{$X}liberty_xref` SET `item` = 'B02' WHERE `item` = '\$02'",
			"UPDATE `{$X}liberty_xref` SET `item` = 'B03' WHERE `item` = '\$03'",
			"UPDATE `{$X}liberty_xref` SET `item` = 'B04' WHERE `item` = '\$04'",
			"DELETE FROM `{$X}liberty_xref` WHERE `item` = '\$01'",
		]]],
	]
);
