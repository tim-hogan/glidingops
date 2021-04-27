<?php
require "./includes/moduleEnvironment.php";
require_once dirname(__FILE__) . '/lrv/vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
  'driver'   => 'mysql',
  'host'     => getenv('DATABASE_HOST'),
  'port' => getenv('DATABASE_PORT'),
  'database' => $devt_environment->getkey('DATABASE_NAME'),
  'username' => $devt_environment->getkey('DATABASE_USER'),
  'password' => $devt_environment->getkey('DATABASE_PW'),
  'charset'  => 'utf8',
  'collation' => 'utf8_unicode_ci',
  'prefix'   => '',
], 'default');

$capsule->addConnection([
  'driver' => 'mysql',
  'host' => getenv('DATABASE_HOST'),
  'port' => getenv('DATABASE_PORT'),
  'database' => $devt_environment->getkey('TRACKS_DATABASE_NAME'),
  'username' => $devt_environment->getkey('TRACKS_DATABASE_USER'),
  'password' => $devt_environment->getkey('TRACKS_DATABASE_PW'),
  'charset' => 'utf8',
  'collation' => 'utf8_unicode_ci',
  'prefix' => '',
], 'tracks');
// Setup the Eloquent ORM…
$capsule->bootEloquent();

?>