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
// xref configuration now lives in liberty_xref_group and liberty_xref_item (content_type_guid='contact').
// These replace the old contact_xref_type and contact_xref_source table defaults.
$gBitInstaller->registerSchemaDefault( CONTACT_PKG_NAME, [

	// --- liberty_xref_group (formerly contact_xref_type: integer xref_type → sort_order, source text → x_group) ---
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_group` (`x_group`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('type',   'contact','Contact Type List',        0,3,'')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_group` (`x_group`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('contact','contact','General Contact Details',   1,3,'')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_group` (`x_group`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('links',  'contact','Linked Contact Items',     2,3,'')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_group` (`x_group`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('account','contact','Account Details',           3,4,'')",

	// --- liberty_xref_item (formerly contact_xref_source) ---
	// group: type
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('\$00','contact','type','Personal',         0,3,'/contact/?type=0', NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('\$01','contact','type','Business',          0,3,'/contact/?type=1', NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('\$02','contact','type','Manufacturer',      0,3,'/contact/?type=2', NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('\$03','contact','type','Distributor',       0,3,'/contact/?type=3', NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('\$04','contact','type','Supplier',          0,3,'/contact/?type=4', NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('\$05','contact','type','Record Company',    0,3,'/contact/?type=5', NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('\$06','contact','type','Record Artist',     0,3,'/contact/?type=6', NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('\$07','contact','type','Cartographer',      0,3,'/contact/?type=7', NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('\$08','contact','type','PHX Client',        0,3,'/contact/?type=8', NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('\$09','contact','type','LSCES Supplier',    0,3,'/contact/?type=9', NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('\$10','contact','type','Paypal Client',     0,3,'/contact/?type=10',NULL)",
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
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('ACC_TO','contact','account','Account Turnover',           0,3,'../vat/?vat=', 'text')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('SAGEID','contact','account','SAGE Account Reference',     0,3,'''sage''',    'text')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_item` (`item`,`content_type_guid`,`x_group`,`cross_ref_title`,`multiple`,`role_id`,`cross_ref_href`,`template`) VALUES ('VAT_NO','contact','account','VAT Number',                 0,3,'../vat/?vat=', 'text')",

] );

// ### Default User Permissions
$gBitInstaller->registerUserPermissions( CONTACT_PKG_NAME, [
	[ 'p_contact_view',    'Can browse the Contact List',       'basic',      CONTACT_PKG_NAME ],
	[ 'p_contact_update',  'Can update the Contact List content','registered', CONTACT_PKG_NAME ],
	[ 'p_contact_create',  'Can create a new Contact List entry','registered', CONTACT_PKG_NAME ],
	[ 'p_contact_admin',   'Can admin Contact List',             'admin',      CONTACT_PKG_NAME ],
	[ 'p_contact_expunge', 'Can remove a Contact entry',         'editors',    CONTACT_PKG_NAME ],
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
