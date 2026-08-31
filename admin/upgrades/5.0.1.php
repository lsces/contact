<?php
/**
 * @package contact
 */

global $gBitInstaller;

$X = BIT_DB_PREFIX;

$gBitInstaller->registerPackageUpgrade(
	[
		'package'     => CONTACT_PKG_NAME,
		'version'     => '5.0.1',
		'description' => 'Drop address_postcode - the OS-style postcode lookup table (86k+ rows '
			.'of add1-4/town/county/grideast/gridnorth per postcode) contact used to enrich the '
			.'hand-entered address xrefs (#C/#I/#R/#S/#T) and derive a map pin when none was set '
			.'manually. Found broken (the enrichment was hardcoded to item \'#S\', which has no '
			.'live data anywhere - real addresses are almost always \'#C\'), and decided against '
			.'fixing in place: postcode/coordinate lookup belongs to mapper (OSM-based) going '
			.'forward, not contact. address/postcode xref data itself (xkey/xkey_ext) is untouched '
			.'- only the derived add1-county/grid-ref enrichment goes. See CLAUDE.md\'s 2026-08-31 '
			.'entry for the full investigation and every code/template site this touched.',
	],
	[
		[ 'QUERY' => [
			'SQL92' => [
				"DROP TABLE `{$X}address_postcode`",
			],
		]],
	]
);
