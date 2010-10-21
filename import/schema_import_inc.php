<?php
$tables = array(

'contact_phx' => "
  content_id I8 PRIMARY,
  contact C(10),
  cltype I4,
  prefix C(35),
  forename C(128),
  surname C(128),
  spouse C(35),
  organisation C(100),
  note C(40),
  memo X,
  contact1 C(128),
  contact2 C(128),
  cname2 C(128),
  contact3 C(128),
  cname3 C(128),
  key1 C(128),
  tel1 C(128),
  mob1 C(128),
  key2 C(128),
  tel2 C(128),
  mob2 C(128),
  key3 C(128),
  tel3 C(128),
  mob3 C(128),
  key4 C(128),
  tel4 C(128),
  mob4 C(128),
  passwd C(128),
  prompt C(128),
  email1 C(128),
  email2 C(128),
  full_start_date C(24),
  payment C(64),
  maintain C(128),
  code C(128),
  key_seal C(128),
  break_seal C(128),
  code C(128),
  start_date T DEFAULT CURRENT_TIMESTAMP,
  last_update_date T DEFAULT CURRENT_TIMESTAMP
",

'contact_sage' => "
  contact_id I8 PRIMARY,
  usn C(16) NOTNULL,
  cltype I4,
  prefix C(35),
  forename C(128),
  surname C(128),
  suffix C(35),
  organisation C(100),
  contact_name C(64),
  telephone C(16),
  fax C(32),
  web C(32),
  analysis_1 C(16),
  analysis_2 C(16),
  analysis_3 C(16),
  dept_number C(8),
  vat_reg_number C(16),
  turnover_mtd C(16),
  turnover_ytd C(16),
  turnover_prior C(16),
  credit_limit C(16),
  terms C(32),
  settlement_due_days C(32),
  settlement_disc_rate C(32),
  def_nom_code C(32),
  def_tax_code C(32)
",

);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( CONTACT_PKG_NAME, $tableName, $tables[$tableName] );
}

?>
