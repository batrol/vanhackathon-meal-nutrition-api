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

    public function user(){
        return $this->belongsTo('GoCanada\Models\User');
    }

    public function searchByName($name){

        $name = strtolower($name);
        return self::whereRaw("LOWER(name) like '%".$name."%'")->with('ingredients')->get();
    }

    public function searchByUser($id){

        return self::where("user_id",[$id])->with('ingredients')->get();
    }

    public function searchByEnergyMin($min){

        return self::whereRaw("energy_total >= $min ")->with('ingredients')->get();
    }

    public function searchByEnergyMax($max){

        return self::whereRaw("energy_total <= $max ")->with('ingredients')->get();
    }

    public function searchByEnergyRange($min,$max){

        return self::whereRaw("energy_total between $min and $max ")->with('ingredients')->get();
    }

}
