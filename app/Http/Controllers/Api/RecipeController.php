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
        $Recipe = new Recipe();
        $ingredients = $Recipe->findOrFail($id)->ingredients;

        // Iterates over ingredients to fill the returning array.
        foreach($ingredients as $ingredient){

            // Get the ingredient identifier and quantity saved.
            $ndbno    = $ingredient->ndbno;
            $quantity = $ingredient->quantity;

            //TODO:COMMENT
            $nutrients = $this->ingredientsRepo->getNutrientsByIngredient($ndbno);

            /** @var Nutrient $nutrient */
            foreach ($nutrients as $nutrient) {

                $nutrientId = $nutrient->getId();
                //var_dump($nutrient);
                // Checks if nutrient is already existent in array
                if(isset($ingredientsNutritionInfo[$nutrientId])){

                    // if nutrient exists add to existing value
                    $nutrientOldValue = $ingredientsNutritionInfo[$nutrientId]['value'];
                    $ingredientsNutritionInfo[$nutrientId]['value'] = $nutrientOldValue+($nutrient->getValue()*$quantity);

                }else{

                    // Sets the values for that nutrient multiplying by the ingredient quantity (total amount).
                    $ingredientsNutritionInfo[$nutrientId]= [
                        'nutrient_id' => $nutrient->getId(),
                        'name'        => $nutrient->getName(),
                        'value'       => $nutrient->getValue() * $quantity,
                        'unit'        => $nutrient->getUnit(),
                        'group'       => $nutrient->getGroup()
                    ];
                }
            }
        }

        // return success with json of nutrients
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

    /**
     * @param $name
     * @return json
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