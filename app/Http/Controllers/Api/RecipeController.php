<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GoCanada\Models\Recipe;


class RecipeController extends Controller
{
    public function nutritionInfo($id)
    {
        $Recipe = new Recipe();

       return $Recipe->find($id);
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
}
