<?php

require 'lrv/vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;

$dotenv = new Dotenv\Dotenv(__DIR__.'/lrv');
$dotenv->load();

$capsule = new Capsule;
$capsule->addConnection([
  'driver'   => 'mysql',
  'host'     => getenv('DB_HOST'),
  'database' => getenv('DB_DATABASE'),
  'username' => getenv('DB_USERNAME'),
  'password' => getenv('DB_PASSWORD'),
  'charset'  => 'utf8',
  'collation' => 'utf8_unicode_ci',
  'prefix'   => '',
], 'default');

$capsule->addConnection([
  'driver' => 'mysql',
  'host' => env('DB_HOST'),
  'port' => env('DB_PORT'),
  'database' => env('DB_TRACKS_DATABASE'),
  'username' => env('DB_TRACKS_USERNAME'),
  'password' => env('DB_TRACKS_PASSWORD'),
  'charset' => 'utf8',
  'collation' => 'utf8_unicode_ci',
  'prefix' => '',
], 'tracks');
// Setup the Eloquent ORM…
$capsule->bootEloquent();

?>