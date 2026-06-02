<?php
/**
 * @package contact
 * @subpackage functions
 */

require_once '../kernel/includes/setup_inc.php';

header( 'Location: ' . CONTACT_PKG_URL . 'list_contacts.php?' . http_build_query( $_GET ) );
die;
