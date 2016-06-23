<?php
return [
'default' => 'mysql',
'host'      => '127.0.0.1',
'database'  => 'ipos',
'usrname'  => 'ospos',
'password'  => '',


'sqlite' => [
	'driver'   => 'sqlite',
	'prefix'   => '',
],
	
'mysql' => [
	'driver'    => 'mysql',
	'charset'   => 'utf8',
	'collation' => 'utf8_unicode_ci',
	'prefix'    => '',
	'strict'    => false,
	'port'		=> '3306',
],

'pgsql' => [
	'driver'   => 'pgsql',
	'charset'  => 'utf8',
	'prefix'   => '',
	'schema'   => 'public',
	'port'		=> '',
]
];