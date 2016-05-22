<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    protected $faker;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::getDriverName() == 'sqlite')
            DB::unprepared('PRAGMA foreign_keys = OFF;');
        else //case 'mysql'
            DB::unprepared('SET FOREIGN_KEY_CHECKS = 0;');

        Model::unguard();

        $this->call(UserSeeder::class);
        $this->call(RecipeSeeder::class);
        $this->call(IngredientRecipeSeeder::class);

        Model::reguard();

        if (DB::getDriverName() == 'sqlite')
            DB::unprepared('PRAGMA foreign_keys = ON;');
        else //case 'mysql'
            DB::unprepared('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
