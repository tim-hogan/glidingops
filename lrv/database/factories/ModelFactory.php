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
    'organisation' => function() {
      return factory(App\Models\Organisation::class)->make();
    }
  ];
});

$factory->define((App\Models\Organisation::class), function(Faker\Generator $faker){
  return [
    'name' => 'Wellington Gliding Club',
    'timezone' => 'Pacific/Auckland',
  ];
});