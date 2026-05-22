<?php
/**
 * @package contact
 */

global $gBitInstaller;

$gBitInstaller->registerPackageUpgrade(
	[
		'package'     => 'contact',
		'version'     => '5.0.2',
		'description' => 'Drop old contact_xref, contact_xref_source and contact_xref_type tables now that data is in liberty_xref tables.',
	],
	[
		[ 'DATADICT' => [
			[ 'DROPTABLE'    => [ 'contact_xref', 'contact_xref_source', 'contact_xref_type' ] ],
			[ 'DROPSEQUENCE' => [ 'contact_xref_seq' ] ],
		]],
	]
);
