<?php
$tables = array(

'contact' => "
  content_id I8 PRIMARY,
  parent_id I8 DEFAULT 0,
  address_id I8 DEFAULT 0,
  role_id I4,
  xkey C(32)
",

'contact_xref' => "
  xref_id I8 PRIMARY,
  content_id I8 NOTNULL,
  source C(20) PRIMARY,
  xorder I2
  xref I8,
  xkey C(32),
  xkey_ext C(250),
  data X,
  start_date T,
  last_update_date T,
  entry_date T,
  end_date T,
  ",

'contact_xref_type' => "
  xref_type I2 PRIMARY,
  source C(20),
  title C(64),
  role_id I4,
  type_href C(256)
  ",

'contact_xref_source' => "
  source C(20) PRIMARY,
  cross_ref_title C(64),
  xref_type I2,
  role_id I4,
  cross_ref_href C(256),
  data X
  ",

'contact_address' => "
  content_id I8 PRIMARY,
  address_id I8,
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
  country C(80),
  country_id I4,
  last_update_date T DEFAULT CURRENT_TIMESTAMP
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
