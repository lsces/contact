<?php
/**
 * @package contact
 */

global $gBitInstaller;

$gBitInstaller->registerPackageUpgrade(
	[
		'package'     => 'contact',
		'version'     => '5.0.1',
		'description' => 'Migrate contact_xref_type, contact_xref_source and contact_xref data into liberty_xref tables with content_type_guid=\'contact\'.',
	],
	[
		[ 'QUERY' => [
			'SQL92' => [
				// contact_xref_type: integer xref_type becomes sort_order; text source becomes the xref_type key
				"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_type`
					(`xref_type`, `content_type_guid`, `title`, `sort_order`, `role_id`, `type_href`)
				SELECT
					`source`, 'contact', `title`, `xref_type`, `role_id`, `type_href`
				FROM `" . BIT_DB_PREFIX . "contact_xref_type`",

				// contact_xref_source: integer xref_type joined to get the text key
				"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source`
					(`source`, `content_type_guid`, `xref_type`, `cross_ref_title`, `multi`, `role_id`, `cross_ref_href`, `template`, `data`)
				SELECT
					cs.`source`, 'contact', ct.`source`, cs.`cross_ref_title`, cs.`multi`, cs.`role_id`, cs.`cross_ref_href`, cs.`template`, cs.`data`
				FROM `" . BIT_DB_PREFIX . "contact_xref_source` cs
				JOIN `" . BIT_DB_PREFIX . "contact_xref_type` ct ON cs.`xref_type` = ct.`xref_type`",

				// contact_xref: direct column mapping, xref_id preserved
				"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref`
					(`xref_id`, `content_id`, `source`, `xorder`, `xref`, `xkey`, `xkey_ext`, `data`, `start_date`, `last_update_date`, `entry_date`, `end_date`)
				SELECT
					`xref_id`, `content_id`, `source`, `xorder`, `xref`, `xkey`, `xkey_ext`, `data`, `start_date`, `last_update_date`, `entry_date`, `end_date`
				FROM `" . BIT_DB_PREFIX . "contact_xref`",
			],
		]],
	]
);
