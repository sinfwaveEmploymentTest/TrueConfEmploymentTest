<?php

namespace App;

// Variables
$ds = DIRECTORY_SEPARATOR;
$dir = __DIR__;
$ctHeader = 'Content-Type';
$jsonCt = 'application/json';

// Config
$config = [
	
// project info
'info' =>
[
	'name' => 'TrueConf Json Api Test',
	'developer' => 'Vladimir Anatolievich Gorbachev'
],

// response headers type
'headers' =>
[
	'json' =>
	[
		'header' => $ctHeader,
		'type' => $jsonCt
	]
],

// database credentials
'database' =>
[
	// MySQL
	'mysql' =>
	[
		'host' => 'localhost',
		'name' => 'test',
		'user' => 'root',
		'pass' => ''
	],
	
	// JSON
	'json' =>
	[
		'path' => "{$dir}{$ds}Database{$ds}Json{$ds}",
		'authDbName' => 'Auth.json',
	]
],

// api settings
'api' =>
[
	// JSON api settings
	'json' =>
	[
		// users.api
		'Users' =>
		[
			// API implemented and future methods array list
			'methods' =>
			[
				'AddUser' => true,
				'GetUsersList' => true,
				'GetUserById' => true,
				'EditUserById' => true,
				'DeleteUserById' => true,
				'SomeNewMethodToBeImplemented' => false
			],
			
			// API accepted user fields, true/false - required
			'fields' =>
			[
				'name' => true,
				'company' => false,
			],
			
			// standard API response arguments 
			'responseArg' =>
			[
				'code' => 200,
				'sender' => 'UsersApi',
				'header' => $ctHeader,
				'type' => $jsonCt
			]
			
		]
	]
]

];

/**
 * Config class
 * 
 * @example self Config::get('database.json.path');
 */
final class Config
{
	// main config object
	private static array $conf;
	
	// get config data
	final public static function get(string $param)
	{
		$e = explode(".", $param);
		$c = count($e);
		$p = self::$conf;
		$i = 0;
		while ($i <= $c - 1 )
		{
			$p = $p[ $e[$i] ];
			$i++;
		}
		return $p;
	}
	
	// set config variable
	final public static function set(array $conf)
	{
		self::$conf = $conf;
	}
}
Config::set($config);