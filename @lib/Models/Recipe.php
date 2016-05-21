<?php

namespace GoCanada\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{

    protected $fillable = [
        'name',
        'user_id',
        'visibility',
        'energy_total'
    ];

    protected $table = 'recipe';

    public function ingredients(){
        return $this->hasMany('GoCanada\Models\IngredientRecipe');
    }

    public function users(){
        return $this->hasMany('GoCanada\Models\User');
    }

    public function ById($id){
        return $this->find($id);
    }

}
