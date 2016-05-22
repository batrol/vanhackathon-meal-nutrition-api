<?php

use GoCanada\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::getDriverName() == 'sqlite')
            DB::unprepared('delete from user;');
        else //case 'mysql'
            DB::unprepared('truncate table user');

        $faker = Faker\Factory::create();

        for ($i=0; $i<10; $i++) {
            $user = new User();
            $user->name     = $faker->unique()->name;
            $user->email    = $faker->unique()->email;
            $user->password = bcrypt('123');

            $user->save();
        }
    }
}
