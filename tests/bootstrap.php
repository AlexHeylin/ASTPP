<?php
/**
 * Test bootstrap file
 */

// Set error reporting for tests
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define test constants
define('TEST_ROOT', __DIR__ . '/..');
define('FREESWITCH_ROOT', TEST_ROOT . '/freeswitch/fs');

// Include necessary files for testing
require_once FREESWITCH_ROOT . '/lib/astpp.db.php';
require_once FREESWITCH_ROOT . '/lib/astpp.xml.php';
require_once FREESWITCH_ROOT . '/lib/astpp.cdr.php';
