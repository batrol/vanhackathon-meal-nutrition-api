<?php 
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use GoCanada\Models\IngredientRecipe;

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
	
    public function store(Request $request)
    {
		//basic saving test
        $ingredientRecipe = new IngredientRecipe();

        $ingredientRecipe->recipe_id = $request->recipe_id;
        $ingredientRecipe->ndbno = $request->ndbno;
        $ingredientRecipe->quantity = $request->quantity;

        $ingredientRecipe->save();
		
		return ["OK"];
    }
	
    public function update(Request $request)
    {
		//basic saving test
        $ingredientRecipe = IngredientRecipe::find($id, $request->recipe_id);

        $ingredientRecipe->recipe_id = $request->recipe_id;
        $ingredientRecipe->ndbno = $request->ndbno;
        $ingredientRecipe->quantity = $request->quantity;

        $ingredientRecipe->save();
		
		return ["OK"];
    }
}
