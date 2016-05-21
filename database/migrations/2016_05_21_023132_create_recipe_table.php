<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecipeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            CREATE TABLE `recipe` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT(10) UNSIGNED NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `visibility` ENUM('PUBLIC', 'PRIVATE') NOT NULL,
                `calories_total` INT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NULL,
                PRIMARY KEY (`id`),
                INDEX `recipe_user_id_fk_idx` (`user_id` ASC),
                UNIQUE INDEX `recipe_name_user_id_uk` (`user_id` ASC, `name` ASC),
                CONSTRAINT `recipe_user_id_fk`
                FOREIGN KEY (`user_id`)
                REFERENCES `user` (`id`)
                ON DELETE RESTRICT
                ON UPDATE RESTRICT);
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
