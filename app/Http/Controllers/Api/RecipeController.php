<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class RecipeController extends Controller
{
    public function nutritionInfo($id)
    {
        return [
            "nutrients" => [
                [
                    "nutrient_id"            => 208,
                    "name"                   => "Energy",
                    "value"                  => 500,
                    "unity"                  => "kcal",
                    "group"                  => "Proximates",
                    "daily_value_percentage" => 10
                ]
            ]
        ];
    }
	
    public function store()
    {
        
    }
	
    public function update($id)
    {
        
    }
}
