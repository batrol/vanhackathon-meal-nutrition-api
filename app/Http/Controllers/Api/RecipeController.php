<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GoCanada\Models\Recipe;
use GoCanada\Models\IngredientRecipe;

use GoCanada\Popos\Nutrient;
use GoCanada\Repositories\IngredientsRepositoryInterface;

use GoCanada\Repositories\RecipeRepositoryInterface;
use Illuminate\Http\Request;
use DB;
use Illuminate\Http\Response;
use Validator;


class RecipeController extends Controller
{
    private $ingredientsRepo;
    private $recipeRepo;

    public function __construct(IngredientsRepositoryInterface $ingredientsRepo, RecipeRepositoryInterface $recipeRepo)
    {
        $this->ingredientsRepo = $ingredientsRepo;
        $this->recipeRepo = $recipeRepo;

    }

    // Function responsible for giving Nutrient Information related to a identified Recipe.
    public function nutritionInfo($id)
    {
        // Get all ingredients of the identified Recipe in the database.
        // Also checks if there is no matching result for the $id and gives error response in that case.
        $recipe = new Recipe();
        $ingredients = $recipe->findOrFail($id)->ingredients;

        $ingredientsNutritionInfo = $this->recipeRepo->sumNutritionInfo($ingredients, $this->ingredientsRepo);

        return $this->success(Response::HTTP_OK, null,['nutrients' => $ingredientsNutritionInfo]);
    }

    public function store(Request $request)
    {
        $recipe = new Recipe();

        return $this->save($request, $recipe, "i");
    }

    public function update(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

        return $this->save($request, $recipe, "u");
    }

    private function save(Request $request, Recipe $recipe, $action)
    {
        $data = $request->all();
        $rules = [
            'user_id' => 'required|integer|exists:user,id',
            'name' => 'required|unique:recipe,name,' . $recipe->id,
            'visibility' => 'required|in:PUBLIC,PRIVATE',
            'ingredients' => 'array|required',
        ];
        $ingredientsPost = [];
        if (array_key_exists('ingredients', $data)) {
            foreach ($request->get('ingredients') as $k => $v) {
                $rules['ingredients.' . $k] = 'array|required';
                $rules['ingredients.' . $k . '.ndbno'] = 'required';
                $rules['ingredients.' . $k . '.quantity'] = 'required|numeric';

                $ingredientsPost[$v["ndbno"]] = $v;
            }
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->error(Response::HTTP_BAD_REQUEST, implode(" ", $validator->errors()->all()), $validator->errors()->all());
        }
        
        //$this

        //TODO: calculate the total_energy outside the transaction
        DB::transaction(function () use($recipe, $ingredientsPost, $request) {
            $recipe->user_id = $request->user_id;
            $recipe->name = $request->name;
            $recipe->visibility = $request->visibility;

            $recipe->save();

            $ingredientsRecipe = [];
            $ingredients = $recipe->ingredients;
            foreach ($ingredients as $ingredient) {
                if (array_key_exists($ingredient->nbdno, $ingredientsPost)) {
                    $ingredient->quantity = $ingredientsPost[$ingredient->nbdno]["quantity"];
                    $ingredient->save();

                    $ingredientsRecipe[] = $ingredient->ndbno;
                }
                else {
                    $ingredient->delete();
                }
            }
            foreach ($ingredientsPost as $ingredientPost) {
                if (!in_array($ingredientPost["ndbno"], $ingredientsRecipe)) {
                    $ingredient = new IngredientRecipe();
                    $ingredient->recipe_id = $recipe->id;
                    $ingredient->ndbno = $ingredientPost["ndbno"];
                    $ingredient->quantity = $ingredientPost["quantity"];
                    $ingredient->save();
                }
            }

            //TODO: calculate the total_energy outside the transaction
            $recipe->energy_total = $this->nutritionInfo($recipe->id)->getData("data")["data"]["nutrients"]["208"]["value"];
            $recipe->save();
        });

        if ($action == "i"){
            return $this->success(Response::HTTP_CREATED, "Recipe stored with id: {$recipe->id}!", ["id" => $recipe->id]);
        }

        return $this->success(Response::HTTP_OK, "Recipe with id {$recipe->id} updated!", ["id" => $recipe->id]);
    }

    public function searchByName($name)
    {
        $name = strtolower($name);
        $recipes = $this->recipeRepo->findByName($name);
        return $recipes;
    }

    public function searchByUser($id)
    {
        //TODO: check if it is number
        //TODO: check response in error
        if (!is_numeric($id) || $id<0){
            return ;
        }
        $recipes = $this->recipeRepo->find($id);
        return $recipes;

    }

    public function searchByEnergyMin($min)
    {
        //TODO: check response in error
        if (!is_numeric($min) || $min<0){
            return ;
        }
        $recipes = $this->recipeRepo->findWhere([['energy_total','>=',$min]]);
        return $recipes;
    }

    public function searchByEnergyMax($max)
    {
        //TODO: check response in error
        if ( !is_numeric($max) || $max<0){
            return ;
        }
        $recipes = $this->recipeRepo->findWhere([['energy_total','<=',$max]]);
        return $recipes;
    }

    public function searchByEnergyRange($min,$max)
    {
        //TODO: check response in error
        if (!is_numeric($min) || !is_numeric($max) || $min<0 || $max<0){
            return;
        }
        $recipes = $this->recipeRepo->findWhere([['energy_total','>=',$min],['energy_total','<=',$max]]);
        return $recipes;
    }

    /**
     * @param $id
     * @return JSON
     * This function show the recipe with ingredients.
     */
    public function show($id) {

        // Get all ingredients of the identified Recipe in the database.

        $recipeItem = $this->recipeRepo->find($id);

        // Sets the values for response.
        $response = [
            "name" => $recipeItem->name,
            "user_name" => $recipeItem->user->name,
            "visibility" => $recipeItem->visibility,
            "energy_total" => $recipeItem->energy_total,
            "ingredients" => $recipeItem->ingredients
        ];

        // Set rules for validations
        $rules = [
            'status' => 'string|exists:success',
            'name' => 'string|required',
            'visibility' => 'string|required',
            'energy_total' => 'numeric|required',
        ];
        // Make validation of response based on rules.
        $validator = Validator::make($response, $rules);

        // If fails return message. Or return response with header http.
        if ($validator->fails()) {
            return $this->error(Response::HTTP_BAD_REQUEST, implode(" ", $validator->errors()->all()), $validator->errors()->all());
        } else {
            return $this->success(Response::HTTP_OK, NULL, $response);
        }
    }

}