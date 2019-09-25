<?php

use Illuminate\Database\Seeder;

class OrganisationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('organisations')->insert([
            'id'                        => 1,
            'name'                      => 'Wellington Gliding Club',
            'addr1'                     => 'PO Box 30 200',
            'addr2'                     => 'Lower Hutt',
            'addr3'                     => '',
            'addr4'                     => '',
            'country'                   => 'New Zealand',
            'contact_name'              => 'Flash Gordon',
            'email'                     => 'flash.gordon@gmail.com',
            'timezone'                  => 'Pacific/Auckland',
            'aircraft_prefix'           => 'ZK',
            'tow_height_charging'       => 1,
            'tow_time_based'            => 0,
            'default_location'          => 'Greytown',
            'name_othercharges'         => 'Airways',
            'def_launch_lat'            => '-41.104941',
            'def_launch_lon'            => '175.499121',
            'map_centre_lat'            => '-41.104941',
            'map_centre_lon'            => '175.499121',
            'twitter_consumerKey'       => '????',
            'twitter_consumerSecret'    => '????',
            'twitter_accessToken'       => '????',
            'twitter_accessTokenSecret' => '????',
        ]);
    }
}
