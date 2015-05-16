<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

$config = \Configurator\Constants::getRoot();
$config->system = new \Configurator\Constants($MLOG);

// Figure out what host we are and do the anything specific to that host
// This will define $config->host
require_once("host.config.php");

// Turn error reporting on, since the server shut it off..
error_reporting(E_ALL);

// Location
date_default_timezone_set('America/New_York');

// Propel
use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionManagerSingle;

$serviceContainer = Propel::getServiceContainer();
$serviceContainer->setAdapterClass('diplomacy', 'mysql');
$manager = new ConnectionManagerSingle();
// The constants below are set in hosts.config.php
// Not sure why this no longer uses
// Propel::init(MAYO_MASTER_DIR . "/orm/build/conf/mayofest-conf.php");
$manager->setConfiguration(array (
	'dsn'      => $config->host->db->dsn,
	'user'     => 'www',
	'password' => $config->host->db->pass,
));
// Replace that with
// require_once '/generated-conf/config.php';
$serviceContainer->setConnectionManager('mayofest', $manager);

// Set up logging
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
$MLOG = new Logger('defaultLogger');
//$log_level = $config->host->mode=='production' ? Logger::INFO : Logger::DEBUG;
$log_level = Logger::DEBUG;
$MLOG->pushHandler(new StreamHandler('/tmp/diplomacy.log', $log_level));
Propel::getServiceContainer()->setLogger('defaultLogger', $MLOG);
$config->setLog($MLOG);

// Query logging
$con = Propel::getWriteConnection(\DiplomacyOrm\Map\UserTableMap::DATABASE_NAME);
$config->system->db = $con; // It's been annoying to retrive this everywhere..
//$con->useDebug(true);

// The host log was never set, so set it now
$config->host->setLog($MLOG);

// vim: ts=3 sw=3 noet :
