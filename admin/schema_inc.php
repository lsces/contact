<?php
$tables = [

	'contact'             => "
  content_id I8 PRIMARY,
  parent_id I8 DEFAULT 0,
  address_id I8 DEFAULT 0,
  role_id I4,
  xkey C(32)
",

	'contact_address'     => "
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
  last_update_date T DEFAULT CURRENT_TIMESTAMP,
  cltype I2
",

];

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( CONTACT_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( CONTACT_PKG_NAME, [
	'description' => "Base Contact management package with contact xref and address books 
	designed to be expanded with additional plugins.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
] );

// ### Indexes
$indices = [
	'contact_parent_id_idx'  => [ 'table' => 'contact', 'cols' => 'parent_id', 'opts' => null ],
	'contact_address_id_idx' => [ 'table' => 'contact', 'cols' => 'address_id', 'opts' => null ],
];
$gBitInstaller->registerSchemaIndexes( CONTACT_PKG_NAME, $indices );

// ### Sequences
$sequences = [];
$gBitInstaller->registerSchemaSequences( CONTACT_PKG_NAME, $sequences );

// ### Defaults

// ### Default User Permissions
$gBitInstaller->registerUserPermissions( CONTACT_PKG_NAME, [
	[ 'p_contact_view', 'Can browse the Contact List', 'basic', CONTACT_PKG_NAME ],
	[ 'p_contact_update', 'Can update the Contact List content', 'registered', CONTACT_PKG_NAME ],
	[ 'p_contact_create', 'Can create a new Contact List entry', 'registered', CONTACT_PKG_NAME ],
	[ 'p_contact_admin', 'Can admin Contact List', 'admin', CONTACT_PKG_NAME ],
	[ 'p_contact_expunge', 'Can remove a Contact entry', 'editors', CONTACT_PKG_NAME ],
] );

// ### Default Preferences
$gBitInstaller->registerPreferences( CONTACT_PKG_NAME, [
	[ CONTACT_PKG_NAME, 'contact_default_ordering', 'title_desc' ],
	[ CONTACT_PKG_NAME, 'contact_list_created', 'y' ],
	[ CONTACT_PKG_NAME, 'contact_list_lastmodif', 'y' ],
	[ CONTACT_PKG_NAME, 'contact_list_notes', 'y' ],
	[ CONTACT_PKG_NAME, 'contact_list_title', 'y' ],
	[ CONTACT_PKG_NAME, 'contact_list_user', 'y' ],
] );

// liberty_xref_type columns: xref_type, content_type_guid, title, sort_order, role_id, type_href
// liberty_xref_source columns: source, content_type_guid, xref_type, cross_ref_title, multi, role_id, cross_ref_href, template, data
$gBitInstaller->registerSchemaDefault( CONTACT_PKG_NAME, [
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_type` (`xref_type`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('type',   'contact','Contact Type List',       0,3,'')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_type` (`xref_type`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('contact','contact','General Contact Details', 1,3,'')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_type` (`xref_type`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('links',  'contact','Linked Contact Items',    2,3,'')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_type` (`xref_type`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('alarm',  'contact','Security System Links',   3,3,'')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_type` (`xref_type`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('council','contact','Council reference links',  4,3,'')",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_type` (`xref_type`,`content_type_guid`,`title`,`sort_order`,`role_id`,`type_href`) VALUES ('account','contact','Account Details',          5,4,'')",

	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('\$00','contact','type','Personal',          0,3,'/contact/?type=0', NULL,NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('\$01','contact','type','Business',           0,3,'/contact/?type=1', NULL,NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('\$02','contact','type','Manufacturer',        0,3,'/contact/?type=2', NULL,NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('\$03','contact','type','Distributor',         0,3,'/contact/?type=3', NULL,NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('\$04','contact','type','Supplier',            0,3,'/contact/?type=4', NULL,NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('\$05','contact','type','Record Company',       0,3,'/contact/?type=5', NULL,NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('\$06','contact','type','Record Artist',        0,3,'/contact/?type=6', NULL,NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('\$07','contact','type','Cartographer',         0,3,'/contact/?type=7', NULL,NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('\$08','contact','type','PHX Client',           0,3,'/contact/?type=8', NULL,NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('\$09','contact','type','LSCES Supplier',       0,3,'/contact/?type=9', NULL,NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('\$10','contact','type','Paypal Client',        0,3,'/contact/?type=10',NULL,NULL)",

	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('#C','contact','contact','Contact Address',     0,3,'../nlpg/?uprn=',          'address',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('#E','contact','contact','eMail Address',        1,3,'../contact/?contact_id=', 'text',   NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('#F','contact','contact','Fax',                  1,3,'../contact/?contact_id=', 'text',   NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('#I','contact','contact','Invoice Address',      0,3,'../nlpg/?uprn=',          'address',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('#P','contact','contact','Telephone',            1,3,'../contact/?contact_id=', 'phone',  NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('#R','contact','contact','Residential Address',  0,3,'../nlpg/?uprn=',          'address',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('#S','contact','contact','Service Address',      0,3,'../nlpg/?uprn=',          'address',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('#T','contact','contact','Tenant Address',       0,3,'../nlpg/?uprn=',          'address',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('#W','contact','contact','Web Site Url',         1,3,'../contact/?contact_id=', 'text',   NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('0', 'contact','contact','Free format information',1,3,'../contact/?xref=',     'text',   NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('CON','contact','links', 'Contact',             1,3,'../nlpg/?uprn=',          'text',   NULL)",

	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('#A',  'contact','alarm',  'Alarm Maintainer',             0,3,'../nlpg/?uprn=','text', NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('#K',  'contact','alarm',  'Keyholder',                    1,3,'../nlpg/?uprn=','phone',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('ALARM','contact','alarm', 'Alarm System',                 0,3,'../nlpg/?uprn=','text', NULL)",

	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('CTAX','contact','council','Council Tax',                  0,3,'../nlpg/?uprn=','text',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('ER',  'contact','council','Electoral Roll',               0,3,'../nlpg/?uprn=','text',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('HBEN','contact','council','Housing Benefit',              0,3,'../nlpg/?uprn=','text',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('NNDR','contact','council','National Non-domestic Rates',   0,3,'../nlpg/?uprn=','text',NULL)",

	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('ACC_TO','contact','account','Account Turnover',           0,3,'../vat/?vat=', 'text',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('SAGEID','contact','account','SAGE Account Reference',      0,3,'''sage''',     'text',NULL)",
	"INSERT INTO `" . BIT_DB_PREFIX . "liberty_xref_source` (`source`,`content_type_guid`,`xref_type`,`cross_ref_title`,`multi`,`role_id`,`cross_ref_href`,`template`,`data`) VALUES ('VAT_NO','contact','account','VAT Number',                  0,3,'../vat/?vat=', 'text',NULL)",
] );

// Requirements
$gBitInstaller->registerRequirements( CONTACT_PKG_NAME, [
	'liberty' => [ 'min' => '5.0.0' ],
] );