<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIngredientRecipeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            CREATE TABLE `ingredient_recipe` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `recipe_id` INT(10) UNSIGNED NOT NULL,
                `ndbno` VARCHAR(255) NOT NULL,
                `quantity` FLOAT NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NULL,
                PRIMARY KEY (`id`),
                INDEX `asd_idx` (`recipe_id` ASC),
                CONSTRAINT `ingredient_recipe_recipe_id_fk`
                FOREIGN KEY (`recipe_id`)
                REFERENCES `recipe` (`id`)
                ON DELETE RESTRICT
                ON UPDATE RESTRICT
            );        
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
