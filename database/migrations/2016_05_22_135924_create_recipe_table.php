<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecipeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('recipe', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('recipe_user_id_fk_idx');
			$table->string('name');
			$table->enum('visibility', array('PUBLIC','PRIVATE'));
			$table->integer('energy_total')->unsigned()->nullable();
			$table->dateTime('created_at')->nullable();
			$table->dateTime('updated_at')->nullable();
			$table->unique(['user_id','name'], 'recipe_name_user_id_uk');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('recipe');
	}

}
