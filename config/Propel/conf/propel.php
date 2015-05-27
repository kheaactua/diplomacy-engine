<?php

require_once(__DIR__ . '/../../../vendor/kheaactua/configurator/src/Constants.php');
$config = \Configurator\Constants::getRoot();
$config->system = new \Configurator\Constants();

// Figure out what host we are and do the anything specific to that host
// This will define $config->host
require_once(__DIR__ . "/../../host.config.php");

$base = $config->host->BASE_DIR;
$orm  = $base.'/orm';

return [
	'propel' => [
		'database' => [
			'connections' => [
				'diplomacy' => [
					'adapter'    => 'mysql',
					'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
					'dsn'        => 'mysql:host=localhost;dbname='. $config->host->db->name,
					'user'       => 'www',
					'password'   => 'hyper',
					'attributes' => []
				]
			]
		],
		'runtime' => [
			'defaultConnection' => 'diplomacy',
			'connections'       => ['diplomacy']
		],
		'generator' => [
			'defaultConnection' => 'diplomacy',
			'connections'       => ['diplomacy']
		],
		'paths' => [
			'schemaDir'    => "$orm/sql",
			'outputDir'    => "$orm/classes",
			'phpDir'       => "$orm/classes",
			'phpConfDir'   => "$orm/conf",
			'migrationDir' => "$orm/migrations",
			'sqlDir'       => "$orm/sql",
		],
	]
];

// vim: ts=3 sw=3 noet :
