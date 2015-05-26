<?php

/**
 * This file originally used constants (define's) to define
 * everything, but has been recently rewritten to use the
 * configurator.  The consequences are that things are still
 * defined (for backwards compatibility), but the config object
 * is what should be used.
 **/

// Define a host configurator
$config = Configurator\Constants::getRoot();
$config->host = new     \Configurator\Constants;
$config->host->db = new \Configurator\Constants;


//
// Set host specific settings
$hostname = trim(`hostname`);
if (strcasecmp($hostname, 'khea') == 0)
	$config->host->HOST = 'khea';
elseif (strcasecmp($hostname, 'eos') == 0)
	$config->host->HOST = 'eos';
elseif (strcasecmp($hostname, 'pontus.cee.carleton.ca') == 0)
	$config->host->HOST = 'pontus';
else
	$config->host->HOST = 'other';


// First, let's determine is this is a CLI call or not.
$config->host->INTERFACE_TYPE=(php_sapi_name() === 'cli' ? 'cli' : 'web');

if ($config->host->INTERFACE_TYPE === 'web' && isset($_SERVER)) {
	$domain = array_key_exists('SERVER_NAME', $_SERVER)?$_SERVER['SERVER_NAME']:'www.CHANGEME.org';
} elseif ($config->host->INTERFACE_TYPE === 'web' && !isset($_SERVER)) {
	$domain = 'dev.CHANGEME.org';
} else {
	// Get our scrpt name
	$master_dir = __DIR__;

	// No matter what, we'll need the master in the include path..
	ini_set('include_path', $master_dir.':'.ini_get('include_path'));

	// Tricky to figure out..
	if ($config->host->host == 'khea' && strstr($master_dir, 'www.CHANGEME.org'))
		$domain = 'dev.CHANGEME.org';
	elseif ($config->host->host == 'pontus' && strstr($master_dir, 'www.CHANGEME.org'))
		$domain = 'CHANGEME.org';
	else
		$domain = '';
}

$config->host->domain=$domain;

if (file_exists(dirname(__FILE__).'/host.local.php')) {
	// Just define these..
	$config->host->PEAR_PATH = '';
	$config->host->db->name = '';
	$config->host->db->host = '';
	$config->host->db->pass = '';
	$config->host->db->dsn = '';
	$config->host->BASE_DIR = '';
	$config->host->BASE_ABS_URL = '';
	$config->host->CMD_PDFLATEX = '';
	$config->host->mode = '';
	$config->host->IS_PRODUCTION_HOST = false;
	$config->host->IS_STAGE_HOST = false;
	include_once('host.local.php');

	if (!strlen($config->host->db->dsn))
		$config->host->db->dsn  = sprintf('mysql:host=%s;dbname=%s', $config->host->db->host, $config->host->db->name);
} else {
	print "Cannot find host file\n";
	$MLOG->info('host.local.php not found!');
}


// These are constant
// Useful for knowing for testing too
$config->host->PRODUCTION_URL  = 'www.mayofest.org';
$config->host->DEVELOPMENT_URL = 'dev.mayofest.org';
$config->host->STAGE_URL       = 'stage.mayofest.org';


//
// Privlledged users (see debug info)
//

if ($config->host->INTERFACE_TYPE == 'web')  {
	// Are we connecting from a development machine?
	$ips = array('192.168.0.98', '134.117.78.53', '134.117.78.52', '76.10.149.171');
	// Is our host a development machine?
	$PRIV_USER = array_search($_SERVER['REMOTE_ADDR'], $ips) !== false;

	// Is the user requesting access?
	if (array_key_exists('PRIV_USER', $_REQUEST)) {
		if (strcasecmp($_REQUEST['PRIV_USER'], 'yes') == 0)
			$PRIV_USER = $PRIV_USER|true;
		else
			$PRIV_USER = $PRIV_USER|false;
	}

	// Are we on the development site?
	$PRIV_USER = $PRIV_USER|($_SERVER['SERVER_NAME'] == 'dev.mayofest.org');
} else {
	$PRIV_USER = true;
}

// Set the constant
$config->host->PRIV_USER = $PRIV_USER;

if (!strlen($config->host->BASE_DIR)) {
	trigger_error("Configuration constants are not defined!");
}

// vim: ts=3 sw=3 noet :
