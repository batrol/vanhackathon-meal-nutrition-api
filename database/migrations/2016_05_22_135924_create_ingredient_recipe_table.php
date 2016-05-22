<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIngredientRecipeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ingredient_recipe', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('recipe_id')->unsigned()->index('asd_idx');
			$table->string('ndbno');
			$table->float('quantity', 10, 0);
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ingredient_recipe');
	}

}
