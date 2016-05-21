<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GoCanada\Models\Recipe;
use GoCanada\Models\IngredientRecipe;

use Illuminate\Http\Request;


class RecipeController extends Controller
{

    // Function responsible for giving Nutrient Information related to a identified Recipe.
    public function nutritionInfo($id)
    {
        // Get all ingredients of the identified Recipe in the database.
        // Also checks if there is no matching result for the $id and gives error response in that case.
        $Recipe = new Recipe();
        $ingredients = $Recipe->findOrFail($id)->ingredients;

        // Iterates over ingredients to fill the returning array.
        foreach($ingredients as $ingredient){

            // Get the ingredient identifier and quantity saved.
            $ndbno = $ingredient->ndbno;
            $quantity = $ingredient->quantity;

            // Consumes the USDA api that contains nutrition information about each ingredient.
            $apiUrl = 'http://api.nal.usda.gov/ndb/reports/?ndbno='.$ndbno.'&type=b&format=json&api_key=DEMO_KEY';
            $externalData = file_get_contents($apiUrl);
            $apiIngredient = json_decode($externalData, true);

            // Iterates over nutrients to find calories information and fills array that will be returned.
            $nutrients = $apiIngredient['report']['food']['nutrients'];
            foreach( $nutrients as $nutrient){

                // Finds if nutrient is Energy(calorie).
                if ($nutrient['name']=='Energy'){

                    // Sets the values for that nutrient multiplying by the ingredient quantity (total amount).
                    $ingredientsNutritionInfo[]= [
                        'name'  => 'Energy',
                        'value' => $nutrient['value']*$quantity
                    ];

                    // Leaves iteration as soon as possible.
                    break;
                }
            }
        }
        return $ingredientsNutritionInfo;


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
	
    public function update(Request $request, $id)
    {
		//basic saving test
        $ingredientRecipe = IngredientRecipe::find($id);

        $ingredientRecipe->recipe_id = $request->recipe_id;
        $ingredientRecipe->ndbno = $request->ndbno;
        $ingredientRecipe->quantity = $request->quantity;

        $ingredientRecipe->save();
		
		return ["OK"];
    }
}
