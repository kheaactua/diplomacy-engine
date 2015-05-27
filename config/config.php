<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

$config = \Configurator\Constants::getRoot();
$config->system = new \Configurator\Constants($MLOG);

// Figure out what host we are and do the anything specific to that host
// This will define $config->host
require_once("host.config.php");

$config->host->data = $config->host->BASE_DIR . '/games';

// Turn error reporting on, since the server shut it off..
error_reporting(E_ALL);

// Location
date_default_timezone_set('America/New_York');

// Propel
use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionManagerSingle;

require_once $config->host->BASE_DIR . '/orm/conf/config.php';

// Set up logging
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
$MLOG = new Logger('defaultLogger');
//$log_level = $config->host->mode=='production' ? Logger::INFO : Logger::DEBUG;
$log_level = Logger::DEBUG;
$MLOG->pushHandler(new StreamHandler($config->host->log_file, $log_level));
Propel::getServiceContainer()->setLogger('defaultLogger', $MLOG);
$config->setLog($MLOG);

// Query logging
$con = Propel::getWriteConnection(\DiplomacyOrm\Map\GameTableMap::DATABASE_NAME);
$config->system->db = $con; // It's been annoying to retrive this everywhere..
//$con->useDebug(true);

// The host log was never set, so set it now
$config->host->setLog($MLOG);

// Some ansi colours.. always useful..
$s="\033[1;";
$config->ansi = new Configurator\Constants($MLOG);
$config->ansi->red="{$s}31m";
$config->ansi->blue="{$s}34m";
$config->ansi->green="{$s}32m";
$config->ansi->cyan="{$s}36m";
$config->ansi->yellow="{$s}33m";
$config->ansi->clear="{$s}0m";

// vim: ts=3 sw=3 sts=3 noet :
