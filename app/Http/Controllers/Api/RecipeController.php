<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GoCanada\Models\Recipe;
use GoCanada\Models\IngredientRecipe;

use GuzzleHttp\Client;

use Illuminate\Http\Request;
use DB;
use Validator;


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
        $recipe = new Recipe();

        return $this->save($request, $recipe);
    }
	
    public function update(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

        return $this->save($request, $recipe);
    }

    private function save(Request $request, Recipe $recipe)
    {
        $rules = [
            'name' => 'required|unique:recipe',
            'visibility' => 'required',
        ];
        foreach($request->get('ingredients') as $k => $v){
            $rules['ingredients.'.$k.'.ndbno'] = 'required';
            $rules['ingredients.'.$k.'.quantity'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ["status" => "error", "message" => implode(" ", $validator->errors()->all()), "errors" => $validator->errors()->all()];
        }

        $recipe->name = $request->name;
        $recipe->visibility = $request->visibility;

        var_dump($recipe); exit;


        $ingredientRecipe->recipe_id = $request->recipe_id;
        $ingredientRecipe->ndbno = $request->ndbno;
        $ingredientRecipe->quantity = $request->quantity;
        //remember to calculate the energy_total

        $ingredientRecipe->save();

        return ["OK"];
    }

    public function searchByName($name)
    {

        $recipe = new Recipe();
        $recipes = $recipe->searchByName($name);
        return $recipes;
    }

    public function searchByUser($id)
    {
        //TODO: check if it is number
        //TODO: check response in error
        if (!is_numeric($id) || $id<0){
            return ;
        }
        $recipe = new Recipe();
        $recipes = $recipe->searchByUser($id);
        return $recipes;

    }

    public function searchByCaloriesMin($min)
    {
        //TODO: check response in error
        if (!is_numeric($min) || $min<0){
            return ;
        }
        $recipe = new Recipe();
        $recipes = $recipe->searchByCaloriesMin($min);
        return $recipes;
    }

    public function searchByCaloriesMax($max)
    {
        //TODO: check response in error
        if ( !is_numeric($max) || $max<0){
            return ;
        }
        $recipe = new Recipe();
        $recipes = $recipe->searchByCaloriesMax($max);
        return $recipes;
    }

    public function searchByCaloriesRange($min,$max)
    {
        //TODO: check response in error
        if (!is_numeric($min) || !is_numeric($max) || $min<0 || $max<0){
            return ;
        }
        $recipe = new Recipe();
        $recipes = $recipe->searchByCaloriesRange($min,$max);
        return $recipes;
    }

}