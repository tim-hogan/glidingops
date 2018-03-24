<?php

require 'lrv/vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;

$dotenv = new Dotenv\Dotenv(__DIR__.'/lrv');
$dotenv->load();

$capsule = new Capsule;
$capsule->addConnection([
 'driver'   => getenv('DB_CONNECTION'),
 'host'     => getenv('DB_HOST'),
 'database' => getenv('DB_DATABASE'),
 'username' => getenv('DB_USERNAME'),
 'password' => getenv('DB_PASSWORD'),
 'charset'  => 'utf8',
 'collation' => 'utf8_unicode_ci',
 'prefix'   => '',
]);
// Setup the Eloquent ORM…
$capsule->bootEloquent();

?>