<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GoCanada\Models\Recipe;
use GoCanada\Models\IngredientRecipe;

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

    /**
     * @param $id
     * @return JSON
     * Function responsible for giving Nutrient Information related to an identified Recipe.
     */
    public function nutritionInfo($id)
    {
        // Get all ingredients of the identified Recipe in the database.
        // Also checks if there is no matching result for the $id and gives error response in that case.
        $recipe = new Recipe();
        $ingredients = $recipe->findOrFail($id)->ingredients;

        //get the array with the nutrition info related to given ingredients
        $ingredientsNutritionInfo = $this->recipeRepo->sumNutritionInfo($ingredients, $this->ingredientsRepo);

        // return success with json of nutrients
        return $this->success(Response::HTTP_OK, null,['nutrients' => $ingredientsNutritionInfo]);
    }

    /**
     * @param Request $request
     * @return JSON
     * This function receives the recipe data and saves it to the DB. It returns the id of the inserted recipe.
     */
    public function store(Request $request)
    {
        //get a new recipe, fill it with the given information and stores it in the database
        $recipe = new Recipe();
        return $this->save($request, $recipe, "i");
    }

    /**
     * @param Request $request
     * @param $id
     * @return JSON
     * This function receives the recipe data and updates it on the DB. It returns the id of the updated recipe.
     */
    public function update(Request $request, $id)
    {
        // Get the identified Recipe in the database.
        // Also checks if there is no matching result for the $id and gives error response in that case.
        $recipe = Recipe::findOrFail($id);

        //update the recipe in the database
        return $this->save($request, $recipe, "u");
    }

    /**
     * @param Request $request
     * @param Recipe $recipe
     * @param $action
     * @return JSON
     * This function receives the recipe data and saves it to the DB. It returns the id of the inserted/updated recipe.
     */
    private function save(Request $request, Recipe $recipe, $action)
    {
        //get all the post/put data
        $data = $request->all();

        //validate if the given data match the requirements
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

                $ingredientsPost[$v["ndbno"]] = (object)$v;
            }
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->error(Response::HTTP_BAD_REQUEST, implode(" ", $validator->errors()->all()), $validator->errors()->all());
        }

        //get the array with the nutrition info related to given ingredients
        $ingredientsNutritionInfo = $this->recipeRepo->sumNutritionInfo($ingredientsPost, $this->ingredientsRepo);

        //save the information in the DB
        DB::transaction(function () use($recipe, $ingredientsPost, $request, $ingredientsNutritionInfo) {
            //store recipe information
            $recipe->user_id = $request->user_id;
            $recipe->name = $request->name;
            $recipe->visibility = $request->visibility;
            $recipe->energy_total = $ingredientsNutritionInfo["208"]["value"];

            $recipe->save();

            //insert, update and/or delete the ingredients related the recipe
            $ingredientsRecipe = [];
            $ingredients = $recipe->ingredients;
            //check all the existent recipe's ingredients and update or delete them
            foreach ($ingredients as $ingredient) {
                if (array_key_exists($ingredient->nbdno, $ingredientsPost)) {
                    //update existent ingredients
                    $ingredient->quantity = $ingredientsPost[$ingredient->nbdno]->quantity;
                    $ingredient->save();

                    $ingredientsRecipe[] = $ingredient->ndbno;
                }
                else {
                    //delete ingredients that were removed from the recipe
                    $ingredient->delete();
                }
            }

            //insert new recipe's ingredients
            foreach ($ingredientsPost as $ingredientPost){
                if (!in_array($ingredientPost->ndbno, $ingredientsRecipe)) {
                    $ingredient = new IngredientRecipe();
                    $ingredient->recipe_id = $recipe->id;
                    $ingredient->ndbno = $ingredientPost->ndbno;
                    $ingredient->quantity = $ingredientPost->quantity;
                    $ingredient->save();
                }
            }
        });

        //return the id of the new recipe
        if ($action == "i"){
            return $this->success(Response::HTTP_CREATED, "Recipe stored with id: {$recipe->id}!", ["id" => $recipe->id]);
        }

        //return the id of the updated recipe
        return $this->success(Response::HTTP_OK, "Recipe with id {$recipe->id} updated!", ["id" => $recipe->id]);
    }

    /**
     * @param $name
     * @return JSON
     * This function list all the recipes matching a name
     */
    public function searchByName($name)
    {
        // changes string to lower so an insensitve case search can be done
        $name = strtolower($name);

        //set the input values
        $input = [
            "name" => $name
        ];

        // Set rules for validations
        $rules = [
            'name' => 'string|min:3|required',
        ];

        // Make validation of response based on rules.
        $validator = Validator::make($input, $rules);

        // If fails return message. Or return response with header http.
        if ($validator->fails()) {
            return $this->error(Response::HTTP_BAD_REQUEST, implode(" ", $validator->errors()->all()), $validator->errors()->all());
        }

        $recipes = $this->recipeRepo->findByName($name);

        return $this->success(Response::HTTP_OK, null, ["recipes" => $recipes]);
    }

    /**
     * @param $id
     * @return JSON
     * This function list all the recipes of an user
     */
    public function searchByUser($id)
    {

        //set the input values
        $input = [
            "id" => $id
        ];

        // Set rules for validations
        $rules = [
            'id' => 'numeric|min:1|required',
        ];
        // Make validation of response based on rules.
        $validator = Validator::make($input, $rules);

        // If fails return message. Or return response with header http.
        if ($validator->fails()) {
            return $this->error(Response::HTTP_BAD_REQUEST, implode(" ", $validator->errors()->all()), $validator->errors()->all());
        }

        // retrieves recipes with user id
        $recipes = $this->recipeRepo->findByField('user_id',$id);

        return $this->success(Response::HTTP_OK, null, ["recipes" => $recipes]);

    }

    /**
     * @param $min
     * @return JSON
     * This function list recipes with more than $min of energy (Kcal)
     */
    public function searchByEnergyMin($min)
    {

        ///set the input values
        $input = [
            "min" => $min
        ];

        // Set rules for validations
        $rules = [
            'min' => 'numeric|min:1|required',
        ];
        // Make validation of response based on rules.
        $validator = Validator::make($input, $rules);

        // If fails return message. Or return response with header http.
        if ($validator->fails()) {
            return $this->error(Response::HTTP_BAD_REQUEST, implode(" ", $validator->errors()->all()), $validator->errors()->all());
        }

        $recipes = $this->recipeRepo->findWhere([['energy_total','>=',$min]]);

        return $this->success(Response::HTTP_OK, null, ["recipes" => $recipes]);
    }

    /**
     * @param $max
     * @return JSON
     * This function list recipes with less than $max of energy (Kcal)
     */
    public function searchByEnergyMax($max)
    {

        ///set the input values
        $input = [
            "max" => $max
        ];

        // Set rules for validations
        $rules = [
            'max' => 'numeric|min:1|required',
        ];
        // Make validation of response based on rules.
        $validator = Validator::make($input, $rules);

        // If fails return message. Or return response with header http.
        if ($validator->fails()) {
            return $this->error(Response::HTTP_BAD_REQUEST, implode(" ", $validator->errors()->all()), $validator->errors()->all());
        }
        $recipes = $this->recipeRepo->findWhere([['energy_total','<=',$max]]);

        return $this->success(Response::HTTP_OK, null, ["recipes" => $recipes]);
    }

    /**
     * @param $min
     * @param $max
     * @return JSON
     * This function list recipes by a range of energy (Kcal)
     */
    public function searchByEnergyRange($min,$max)
    {
        ///set the input values
        $input = [
            "min" => $min,
            "max" => $max
        ];

        // Set rules for validations
        $rules = [
            'min' => 'numeric|min:1|required',
            'max' => 'numeric|min:min|required',
        ];
        // Make validation of response based on rules.
        $validator = Validator::make($input, $rules);

        // If fails return message. Or return response with header http.
        if ($validator->fails()) {
            return $this->error(Response::HTTP_BAD_REQUEST, implode(" ", $validator->errors()->all()), $validator->errors()->all());
        }

        $recipes = $this->recipeRepo->findWhere([['energy_total','>=',$min],['energy_total','<=',$max]]);

        return $this->success(Response::HTTP_OK, null, ["recipes" => $recipes]);
    }

    /**
     * @param $id
     * @return JSON
     * This function show the recipe with ingredients.
     */
    public function show($id) {
        // Get all ingredients of the identified Recipe in the database.
        $recipeItem = $this->recipeRepo->find($id);

        //returns the recipe, all its ingredients and its nutrient info.
        return $this->success(Response::HTTP_OK, NULL, [
            "name" => $recipeItem->name,
            "user_name" => $recipeItem->user->name,
            "visibility" => $recipeItem->visibility,
            "energy_total" => $recipeItem->energy_total,
            "ingredients" => $recipeItem->ingredients,
            "nutrientInfo" => $this->recipeRepo->sumNutritionInfo($recipeItem->ingredients, $this->ingredientsRepo),
        ]);
    }

}