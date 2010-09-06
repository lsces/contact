<?php
$tables = array(

'contact' => "
  content_id I8 PRIMARY,
  usn I8 NOTNULL,
  parent_id I8,
  uprn I8,
  nlpg I8,
  ctax I8,
  opfl I8,
  cltype I4,
  prefix C(35),
  forename C(35),
  surname C(35),
  suffix C(35),
  organisation C(100),
  last_update_date T DEFAULT 'NOW',
  note C(40),
  memo X,
  contact1 C(128),
  contact2 C(128),
  contact3 C(128),
  key1 C(128),
  tel1 C(128),
  key2 C(128),
  tel2 C(128),
  key3 C(128),
  tel3 C(128),
  passwd C(64),
  prompt C(64),
  start_date T DEFAULT 'NOW',
  payment C(64),
  maintain C(128),
  code C(128)
",

'contact_xref' => "
  content_id I8 NOTNULL,
  xref_key C(14),
  start_date T,
  last_update_date T,
  entry_date T,
  end_date T,
  source C(20) PRIMARY,
  cross_reference C(22) PRIMARY,
  data X
  ",

'contact_type' => "
  contact_type_id I4 PRIMARY,
  type_name	C(64)
",

'contact_xref_source' => "
  source C(6) PRIMARY,
  cross_ref_title C(64),
  cross_ref_href C(256),
  data X
  ",

'contact_type_map' => "
  content_id I4 PRIMARY,
  contact_type_id I4 PRIMARY,
  type_value	I4
",

'contact_address' => "
  content_id I8 PRIMARY,
  usn I8,
  uprn I8,
  postcode C(10),
  organisation C(100),
  sao C(80),
  pao C(80),
  number C(80),
  street C(250),
  locality C(250),
  town C(80),
  county C(80),
  zone_id I4,
  country_id I4,
  last_update_date T DEFAULT CURRENT_TIMESTAMP
",

'postcode' => "
  postcode C(10),
  add1 C(32),
  add2 C(32),
  add3 C(32),
  add4 C(32),
  town C(20),
  county C(20),
  grideast I4,
  gridnorth I4,
  w_id C(6),
  p_id C(7),
  NHS C(3),
  PCG C(5)
",

);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( CONTACT_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( CONTACT_PKG_NAME, array(
	'description' => "Base Contact management package with contact xref and address books",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

// ### Indexes
$indices = array (
	'contact_contact_id_idx' => array( 'table' => 'contact', 'cols' => 'usn', 'opts' => NULL ),
);
$gBitInstaller->registerSchemaIndexes( CONTACT_PKG_NAME, $indices );

// ### Sequences
$sequences = array (
	'contact_id_seq' => array( 'start' => 1 ),
);
$gBitInstaller->registerSchemaSequences( CONTACT_PKG_NAME, $sequences );

// ### Defaults

// ### Default User Permissions
$gBitInstaller->registerUserPermissions( CONTACT_PKG_NAME, array(
	array('p_contact_view', 'Can browse the Contact List', 'basic', CONTACT_PKG_NAME),
	array('p_contact_update', 'Can update the Contact List content', 'registered', CONTACT_PKG_NAME),
	array('p_contact_create', 'Can create a new Contact List entry', 'registered', CONTACT_PKG_NAME),
	array('p_contact_admin', 'Can admin Contact List', 'admin', CONTACT_PKG_NAME),
	array('p_contact_expunge', 'Can remove a Contact entry', 'editors', CONTACT_PKG_NAME)
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( CONTACT_PKG_NAME, array(
	array( CONTACT_PKG_NAME, 'contact_default_ordering','title_desc'),
	array( CONTACT_PKG_NAME, 'contact_list_created','y'),
	array( CONTACT_PKG_NAME, 'contact_list_lastmodif','y'),
	array( CONTACT_PKG_NAME, 'contact_list_notes','y'),
	array( CONTACT_PKG_NAME, 'contact_list_title','y'),
	array( CONTACT_PKG_NAME, 'contact_list_user','y'),
) );

$gBitInstaller->registerSchemaDefault( CONTACT_PKG_NAME, array(
"INSERT INTO `".BIT_DB_PREFIX."contact_type` VALUES (0, 'Personal')",
"INSERT INTO `".BIT_DB_PREFIX."contact_type` VALUES (1, 'Business')",
"INSERT INTO `".BIT_DB_PREFIX."contact_type` VALUES (2, 'Manufacturer')",
"INSERT INTO `".BIT_DB_PREFIX."contact_type` VALUES (3, 'Distributor')",
"INSERT INTO `".BIT_DB_PREFIX."contact_type` VALUES (4, 'Supplier')",
"INSERT INTO `".BIT_DB_PREFIX."contact_type` VALUES (5, 'Record Company')",
"INSERT INTO `".BIT_DB_PREFIX."contact_type` VALUES (6, 'Record Artist')",
"INSERT INTO `".BIT_DB_PREFIX."contact_type` VALUES (7, 'Cartographer')",

"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`( `source`, `cross_ref_title`, `cross_ref_href` )  VALUES ('0' , 'Free format information', '../contact/?xref=')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`( `source`, `cross_ref_title`, `cross_ref_href` )  VALUES ('#R', 'Residential Address', '../nlpg/?uprn=')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`( `source`, `cross_ref_title`, `cross_ref_href` )  VALUES ('#T', 'Tenant Address', '../nlpg/?uprn=')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`( `source`, `cross_ref_title`, `cross_ref_href` )  VALUES ('#C', 'Correspondence Address', '../nlpg/?uprn=')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`( `source`, `cross_ref_title`, `cross_ref_href` )  VALUES ('#O', 'Owner Address', '../nlpg/?uprn=')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`( `source`, `cross_ref_title`, `cross_ref_href` )  VALUES ('#K', 'Keyholder', '../nlpg/?uprn=')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`( `source`, `cross_ref_title`, `cross_ref_href` )  VALUES ('HBEN', 'Housing Benefit', '../nlpg/?uprn=')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`( `source`, `cross_ref_title`, `cross_ref_href` )  VALUES ('CTAX', 'Council Tax', '../nlpg/?uprn=')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`( `source`, `cross_ref_title`, `cross_ref_href` )  VALUES ('NNDR', 'National Non-domestic Rates', '../nlpg/?uprn=')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`( `source`, `cross_ref_title`, `cross_ref_href` )  VALUES ('ER', 'Electoral Roll', '../nlpg/?uprn=')",
) );


?>
