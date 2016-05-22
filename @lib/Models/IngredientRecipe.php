<?php namespace GoCanada\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientRecipe extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $casts = [
        'id' => 'int',
        'recipe_id' => 'int',
        'quantity'=>'float'
    ];
    protected $table = 'ingredient_recipe';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['recipe_id', 'ndbno', 'quantity'];
}
