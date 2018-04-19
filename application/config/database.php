<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Illuminate\Database\Capsule\Manager as Capsule;

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => '',
	'database' => 'test_codeigniter',
	'dbdriver' => 'mysqli',
	'dbprefix' => 'parf_',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

$capsule = new Capsule;



$capsule->addConnection([
    "driver" => "mysql",
    "host" =>$db['default']['hostname'],
    "database" => $db['default']['database'],
    "username" => $db['default']['username'],
    "password" => $db['default']['password'],
    "charset"  => $db['default']['char_set'],
    "collation"=> $db['default']['dbcollat'],
    "prefix"   => $db['default']['dbprefix']
]);

//Make this Capsule instance available globally.
$capsule->setAsGlobal();

// Setup the Eloquent ORM.
$capsule->bootEloquent();
