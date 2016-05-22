<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToIngredientRecipeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ingredient_recipe', function(Blueprint $table)
		{
			$table->foreign('recipe_id', 'ingredient_recipe_recipe_id_fk')->references('id')->on('recipe')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ingredient_recipe', function(Blueprint $table)
		{
			$table->dropForeign('ingredient_recipe_recipe_id_fk');
		});
	}

}
