<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $org = App\Models\Organisation::where('name', 'Wellington Gliding Club')->first();

        $user = new App\Models\User();
        $user->name = 'Flash Gordon';
        $user->usercode = 'fgordon';
        $user->password = md5('fgordon');
        $user->org = 1;
        $user->expire = '2014-10-27 00:00:00';
        $user->securitylevel = 255;
        $user->member = 1;
        $user->force_pw_reset = 0;

        $org->users()->save($user);
    }
}
