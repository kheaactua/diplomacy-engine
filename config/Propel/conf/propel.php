<?php

$base = '/home/matt/diplomacy-engine2';
$orm = $base.'/orm';

return [
	'propel' => [
		'database' => [
			'connections' => [
				'diplomacy' => [
					'adapter'    => 'mysql',
					'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
					'dsn'        => 'mysql:host=localhost;dbname=diplomacy_gray',
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
