<?php

use GoCanada\Models\Recipe;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared("truncate table recipe");

        $faker = Faker\Factory::create();

        for ($i=0; $i<100; $i++) {
            $recipe = new Recipe();

            $recipe->name         = join(' ', $faker->unique()->words(rand(1, 3)));
            $recipe->user_id      = mt_rand(1, 10);
            $recipe->visibility   = ['PUBLIC', 'PRIVATE'][mt_rand(0, 1)];
            $recipe->energy_total = mt_rand(100, 2000);

            $recipe->save();
        }
    }
}
