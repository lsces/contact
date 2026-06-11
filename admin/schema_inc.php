<?php
$tables = [

	'contact'          => "
  content_id I8 PRIMARY,
  parent_id I8 DEFAULT 0,
  address_id I8 DEFAULT 0,
  role_id I4,
  xkey C(32)
",

	'contact_address'  => "
  content_id I8 PRIMARY,
  address_id I8,
  uprn I8,
  postcode C(10),
  sao C(80),
  pao C(80),
  number C(80),
  street C(250),
  locality C(250),
  town C(80),
  county C(80),
  zone_id I4,
  country C(80),
  country_id I4,
  last_update_date T DEFTIMESTAMP,
  cltype I2
",

	'address_postcode' => "
  postcode C(10) NOTNULL PRIMARY,
  add1 C(32) DEFAULT '',
  add2 C(32) DEFAULT '',
  add3 C(32) DEFAULT '',
  add4 C(32) DEFAULT '',
  town C(20) DEFAULT '',
  county C(20) DEFAULT '',
  grideast C(6) DEFAULT '00000',
  gridnorth C(6) DEFAULT '00000',
  wcd C(6) DEFAULT '',
  nhs C(3) DEFAULT '',
  pcg C(5) DEFAULT '',
  wnw C(32) DEFAULT '',
  wna C(32) DEFAULT '',
  pnp C(32) DEFAULT '',
  pnh C(32) DEFAULT '',
  nnh C(32) DEFAULT '',
  nnr C(32) DEFAULT ''
",

];

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( CONTACT_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( CONTACT_PKG_NAME, [
	'description'  => "Base Contact management package with contact xref and address books
	designed to be expanded with additional plugins.",
	'license'      => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
	'dependencies' => 'liberty',
] );


// ### Indexes
$indices = [
	'contact_parent_id_idx'  => [ 'table' => 'contact', 'cols' => 'parent_id', 'opts' => null ],
	'contact_address_id_idx' => [ 'table' => 'contact', 'cols' => 'address_id', 'opts' => null ],
];
$gBitInstaller->registerSchemaIndexes( CONTACT_PKG_NAME, $indices );

// ### Sequences
$gBitInstaller->registerSchemaSequences( CONTACT_PKG_NAME, [] );

// ### Defaults
// Xref schema: shared groups/items at content_type_guid='contact'; type markers split by sub-type.
// contactperson: 'type' group + $00 item. contactbusiness: 'type' group + $02-$05 items.
$gBitInstaller->registerSchemaDefault( CONTACT_PKG_NAME, [

	// --- liberty_content_types — sub-type handlers for person and business ---
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_content_types` (`content_type_guid`,`content_name`,`content_name_plural`,`handler_class`,`handler_package`,`handler_file`,`maintainer_url`) VALUES ('contactperson','Person Contact','Person Contacts','ContactPerson','contact','ContactPerson.php','http://lsces.co.uk')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_content_types` (`content_type_guid`,`content_name`,`content_name_plural`,`handler_class`,`handler_package`,`handler_file`,`maintainer_url`) VALUES ('contactbusiness','Business Contact','Business Contacts','ContactBusiness','contact','ContactBusiness.php','http://lsces.co.uk')",

	// --- liberty_xref_group ---
	// 'type' group split: one per sub-type (sort_order=0 = type-marker group, excluded from loadXrefInfo display)
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_group` (`x_group`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('type','contactperson', 'Person Type',       0,3,'')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_group` (`x_group`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('type','contactbusiness','Business Type List',0,3,'')",
	// shared groups stay at 'contact' level (loaded via dual-guid IN filter)
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_group` (`x_group`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('contact','contact','General Contact Details',1,3,'')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_group` (`x_group`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('links',  'contact','Linked Contact Items',  2,3,'')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_group` (`x_group`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('account','contact','Account Details',         3,3,'')",

	// --- liberty_xref_item ---
	// group: type — person types
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('P01','contactperson','type','Personal',       0,3,'',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('P02','contactperson','type','MERG Kit Elf',  0,3,'',NULL)",
	// group: type — business subtypes
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('B01','contactbusiness','type','Service',      0,3,'',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('B02','contactbusiness','type','Manufacturer', 0,3,'',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('B03','contactbusiness','type','Distributor',  0,3,'',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('B04','contactbusiness','type','Supplier',     0,3,'',NULL)",
	// group: contact
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('#C','contact','contact','Contact Address',          0,3,'../nlpg/?uprn=',         'address')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('#E','contact','contact','eMail Address',             1,3,'../contact/?contact_id=','text'   )",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('#F','contact','contact','Fax',                       1,3,'../contact/?contact_id=','text'   )",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('#I','contact','contact','Invoice Address',           0,3,'../nlpg/?uprn=',         'address')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('#P','contact','contact','Telephone',                 1,3,'../contact/?contact_id=','phone'  )",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('#R','contact','contact','Residential Address',       0,3,'../nlpg/?uprn=',         'address')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('#S','contact','contact','Service Address',           0,3,'../nlpg/?uprn=',         'address')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('#T','contact','contact','Tenant Address',            0,3,'../nlpg/?uprn=',         'address')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('#W','contact','contact','Web Site Url',              1,3,'../contact/?contact_id=','text'   )",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('0', 'contact','contact','Free format information',   1,3,'../contact/?xref=',      'text'   )",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('CON','contact','contact','Contact',                  1,3,'../nlpg/?uprn=',         'text'   )",
	// group: links
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('SCREF','contact','links','Stock Source Reference',0,3,'../stock/?content_id=','text')",
	// group: account
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('ACC_TO','contact','account','Account Turnover',           0,4,'../vat/?vat=', 'text')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('ACCNO','contact','links','Account Number', 0,3,'',    'text')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('VAT_NO','contact','account','VAT Number',                 0,4,'../vat/?vat=', 'text')",

] );

// ### Default User Permissions
$gBitInstaller->registerUserPermissions( CONTACT_PKG_NAME, [
	[ 'p_contact_view',    'Can browse the Contact List',        'registered', CONTACT_PKG_NAME ],
	[ 'p_contact_create',  'Can create a new Contact List entry','editors',    CONTACT_PKG_NAME ],
	[ 'p_contact_update',  'Can update the Contact List content','editors',    CONTACT_PKG_NAME ],
	[ 'p_contact_expunge', 'Can remove a Contact entry',         'admin',      CONTACT_PKG_NAME ],
	[ 'p_contact_admin',   'Can admin Contact List',             'admin',      CONTACT_PKG_NAME ],
] );

// ### Default Preferences
$gBitInstaller->registerPreferences( CONTACT_PKG_NAME, [
	[ CONTACT_PKG_NAME, 'contact_default_ordering', 'title_desc' ],
	[ CONTACT_PKG_NAME, 'contact_list_created',     'y' ],
	[ CONTACT_PKG_NAME, 'contact_list_lastmodif',   'y' ],
	[ CONTACT_PKG_NAME, 'contact_list_notes',       'y' ],
	[ CONTACT_PKG_NAME, 'contact_list_title',       'y' ],
	[ CONTACT_PKG_NAME, 'contact_list_user',        'y' ],
] );

// ### Requirements
$gBitInstaller->registerRequirements( CONTACT_PKG_NAME, [
	'liberty' => [ 'min' => '5.0.1' ],
] );
