<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Models\Member::class, function(Faker\Generator $faker) {
  return [
    'firstname' => $faker->firstName,
    'surname' => $faker->lastName,
    'displayname' => 'test user',
    'date_of_birth' => '1915-05-30',
    'email' => $faker->safeEmail,
  ];
});

$factory->define(App\Models\Organisation::class, function(Faker\Generator $faker){
  return [
    'name' => 'Wellington Gliding Club',
    'timezone' => 'Pacific/Auckland',
  ];
});

$factory->define(App\Models\Aircraft::class, function(Faker\Generator $faker){
  return [
    'registration' => 'ZK-GGR',
    'rego_short' => 'GGR',
    // 'type' => 1,
    'make_model' => 'DG-1000',
    'seats' => 2,
    'serial' => '10-95 S 67',
    'club_glider' => 1,
    'bookable' => 1,
    'charge_per_minute' => 1.0,
    'max_perflight_charge' => 120,
    'next_annual' => '2000-01-01 00:00:00',
    'next_supplementary' => '2000-01-01 00:00:00',
    'flarm_ICAO' => 'DD51CE',
    'spot_id' => '',
  ];
});

$factory->define(App\Models\Flight::class, function(Faker\Generator $faker){
  return [
    'date' => "2018-07-07 22:38:12",
    'localdate' => 20180708,
    'location' => 'Papawai',
    'towplane' => null,
    'glider' => 'GGR',
    'towpilot' => null,
    'start' => 1531019640000,
    'towland' => null,
    'land' => 1531019760000,
    'height' => null,
    'billing_member1' => null,
    'billing_member2' => null,
    'comments' => null,
    'finalised' => true,
    'deleted' => false,
    'vector' => '',
  ];
});

$factory->define(App\Models\FlightType::class, function(Faker\Generator $faker){
  return [
    'name' => 'Glider',
  ];
});

$factory->define(App\Models\LaunchType::class, function(Faker\Generator $faker){
  return [
    'name' => "Winch",
    'acronym' => "W",
  ];
});

$factory->define(App\Models\BillingOption::class, function(Faker\Generator $faker){
  return [
    'name' => "Charge P2",
    'bill_pic' => 0,
    'bill_p2' => 1,
    'bill_other' => 0,
  ];
});

$factory->define(App\Models\Charge::class, function(Faker\Generator $faker){
  return [
    'name' => "Winch",
    'location' => "Papawai",
    'validfrom' => "2016-01-01 00:00:00",
    'amount' => "45",
    'every_flight' => 0,
    'max_once_per_day' => 0,
    'monthly' => 0,
    'comments' => "",
  ];
});