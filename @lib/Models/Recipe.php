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

    public function searchByName($name){

        $name = strtolower($name);
        return self::whereRaw("LOWER(name) like '%".$name."%'")->with('ingredients')->get();
    }

    public function searchByUser($id){

        return self::where("user_id",[$id])->with('ingredients')->get();
    }

    public function searchByCaloriesMin($min){

        return self::whereRaw("calories_total >= $min ")->with('ingredients')->get();
    }

    public function searchByCaloriesMax($max){

        return self::whereRaw("calories_total <= $max ")->with('ingredients')->get();
    }

    public function searchByCaloriesRange($min,$max){

        return self::whereRaw("calories_total between $min and $max ")->with('ingredients')->get();
    }


}
