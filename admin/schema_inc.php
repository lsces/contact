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
  end_date T
  ",

'contact_xref_source' => "
  source C(20) PRIMARY,
  cross_ref_title C(64),
  xref_type I2,
  multi I2,
  role_id I4,
  cross_ref_href C(256),
  template C(32),
  data X
  ",

'contact_xref_type' => "
  xref_type I2 PRIMARY,
  source C(20),
  title C(64),
  role_id I4,
  type_href C(256)
  ",

'contact_address' => "
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
	'contact_parent_id_idx' => array( 'table' => 'contact', 'cols' => 'parent_id', 'opts' => NULL ),
	'contact_address_id_idx' => array( 'table' => 'contact', 'cols' => 'address_id', 'opts' => NULL ),
);
$gBitInstaller->registerSchemaIndexes( CONTACT_PKG_NAME, $indices );

// ### Sequences
$sequences = array (
	'contact_xref_seq' => array( 'start' => 1 ),
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
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_type` VALUES ('0', 'type', 'Contact Type List', '3', '')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_type` VALUES ('1', 'contact', 'General Contact Details', '3', '')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_type` VALUES ('2', 'links', 'Linked Contact Items', '3', '')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_type` VALUES ('3', 'alarm', 'Security System Links', '3', '')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_type` VALUES ('4', 'council', 'Council reference links', '3', '')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_type` VALUES ('5', 'account', 'Account Details', '4', '')",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('$00', 'Personal', '0', '0', '3', '/contact/?type=0', NULL, NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('$01', 'Business', '0', '0', '3', '/contact/?type=1', NULL, NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('$02', 'Manufacturer', '0', '0', '3', '/contact/?type=2', NULL, NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('$03', 'Distributor', '0', '0', '3', '/contact/?type=3', NULL, NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('$04', 'Supplier', '0', '0', '3', '/contact/?type=4', NULL, NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('$05', 'Record Company', '0', '0', '3', '/contact/?type=5', NULL, NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('$06', 'Record Artist', '0', '0', '3', '/contact/?type=6', NULL, NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('$07', 'Cartographer', '0', '0', '3', '/contact/?type=7', NULL, NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('$08', 'PHX Client', '0', '0', '3', '/contact/?type=8', NULL, NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('$09', 'LSCES Supplier', '0', '0', '3', '/contact/?type=9', NULL, NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('$10', 'Paypal Client', '0', '0', '3', '/contact/?type=10', NULL, NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('#C', 'Correspondence Address', '1', '0', '3', '../nlpg/?uprn=', 'address', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('#E', 'eMail Address', '1', '1', '3', '../contact/?contact_id=', 'text', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('#F', 'Fax', '1', '1', '3', '../contact/?contact_id=', 'text', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('#O', 'Owner Address', '1', '0', '3', '../nlpg/?uprn=', 'address', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('#P', 'Telephone', '1', '1', '3', '../contact/?contact_id=', 'phone', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('#R', 'Residential Address', '1', '0', '3', '../nlpg/?uprn=', 'address', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('#T', 'Tenant Address', '1', '0', '3', '../nlpg/?uprn=', 'address', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('#W', 'Web Site Url', '1', '1', '3', '../contact/?contact_id=', 'text', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('0', 'Free format information', '1', '1', '3', '../contact/?xref=', 'text', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('CON', 'Contact', '1', '1', '3', '../nlpg/?uprn=', 'text', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('#A', 'Alarm Maintainer', '3', '0', '3', '../nlpg/?uprn=', 'text', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('#K', 'Keyholder', '3', '1', '3', '../nlpg/?uprn=', 'phone', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('ALARM', 'Alarm System', '3', '0', '3', '../nlpg/?uprn=', 'text', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('CTAX', 'Council Tax', '4', '0', '3', '../nlpg/?uprn=', 'text', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('ER', 'Electoral Roll', '4', '0', '3', '../nlpg/?uprn=', 'text', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('HBEN', 'Housing Benefit', '4', '0', '3', '../nlpg/?uprn=', 'text', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('NNDR', 'National Non-domestic Rates', '4', '0', '3', '../nlpg/?uprn=', 'text', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('ACC_TO', 'Account Turnover', '5', '0', '3', '../vat/?vat=', 'text', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('SAGEID', 'SAGE Account Reference', '5', '0', '3', '''sage''', 'text', NULL)",
"INSERT INTO `".BIT_DB_PREFIX."contact_xref_source`  VALUES ('VAT_NO', 'VAT Number', '5', '0', '3', '../vat/?vat=', 'text', NULL)",
) );


?>
