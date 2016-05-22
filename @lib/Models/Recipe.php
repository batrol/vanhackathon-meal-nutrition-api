<?php namespace GoCanada\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    // defining attribute response types
    protected $casts = [
        'id' => 'int',
        'user_id' => 'int',
        'energy_total'=>'float'
    ];

    protected $fillable = [
        'name',
        'user_id',
        'visibility',
        'energy_total'
    ];

    protected $table = 'recipe';

    // Relationship with IngredientRecipe
    public function ingredients(){
        return $this->hasMany('GoCanada\Models\IngredientRecipe');
    }

    // Relationship with User
    public function user(){
        return $this->belongsTo('GoCanada\Models\User');
    }

}
