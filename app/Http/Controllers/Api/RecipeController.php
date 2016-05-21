<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GoCanada\Models\Recipe;
use GoCanada\Models\IngredientRecipe;

use GuzzleHttp\Client;

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
            $apiKey= 'IlAoU2IJI9TWWN7wmupWrZFwOfbyjOwNmTS2eZsy';
            $apiUrl = 'http://api.nal.usda.gov/ndb/reports/?ndbno='.$ndbno.'&type=f&format=json&api_key='.$apiKey;
            $client = new Client();
            $response = $client->request('GET', $apiUrl);
            $responseBody =  $response->getBody();

            if ($response->getStatusCode() != 200){
                $returnData = array(
                    'status' => 'error',
                    'message' => 'No Api Response'
                );
                return response()->json($returnData, 500);
            }
            $apiIngredient = json_decode($responseBody, true);

            // Iterates over nutrients to find calories information and fills array that will be returned.
            $nutrients = $apiIngredient['report']['food']['nutrients'];

            foreach( $nutrients as $nutrient){

                $nutrientId = $nutrient['nutrient_id'];
                //var_dump($nutrient);
                // Checks if nutrient is already existent in array
                if(isset($ingredientsNutritionInfo[$nutrientId])){

                    // if nutrient exists add to existing value
                    $nutrientOldValue = $ingredientsNutritionInfo[$nutrientId]['value'];
                    $ingredientsNutritionInfo[$nutrientId]['value'] = $nutrientOldValue+($nutrient['value']*$quantity);

                }else{

                    // Sets the values for that nutrient multiplying by the ingredient quantity (total amount).
                    $ingredientsNutritionInfo[$nutrientId]= [
                        'name'  => $nutrient['name'],
                        'value' => $nutrient['value']*$quantity,
                        'unit'  => $nutrient['unit'],
                        'group' => $nutrient['group']
                    ];
                }

            }

        }

        return ['nutrients'=>$ingredientsNutritionInfo];
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
