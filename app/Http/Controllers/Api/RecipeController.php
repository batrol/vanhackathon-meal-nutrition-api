<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GoCanada\Models\Recipe;
use GoCanada\Models\IngredientRecipe;

use GoCanada\Popos\Nutrient;
use GoCanada\Repositories\IngredientsRepositoryInterface;
use GuzzleHttp\Client;

use Illuminate\Http\Request;
use DB;
use Illuminate\Http\Response;
use Validator;


class RecipeController extends Controller
{
    private $ingredientsRepo;

    public function __construct(IngredientsRepositoryInterface $ingredientsRepo)
    {
        $this->ingredientsRepo = $ingredientsRepo;
        
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

        return ['nutrients' => array_values($ingredientsNutritionInfo)];
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
            'name' => 'required|unique:recipe',
            'visibility' => 'required',
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
            $recipe->energy_total = $this->nutritionInfo($recipe->id)["nutrients"]["208"]["value"];
            $recipe->save();
        });

        if ($action == "i"){
            $this->success(Response::HTTP_CREATED, "Recipe stored with id: {$recipe->id}!");
        }

        $this->success(Response::HTTP_OK, "Recipe with id {$recipe->id} updated!");
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

    public function searchById($id)
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

    public function searchByEnergyMin($min)
    {
        //TODO: check response in error
        if (!is_numeric($min) || $min<0){
            return ;
        }
        $recipe = new Recipe();
        $recipes = $recipe->searchByEnergyMin($min);
        return $recipes;
    }

    public function searchByEnergyMax($max)
    {
        //TODO: check response in error
        if ( !is_numeric($max) || $max<0){
            return ;
        }
        $recipe = new Recipe();
        $recipes = $recipe->searchByEnergyMax($max);
        return $recipes;
    }

    public function searchByEnergyRange($min,$max)
    {
        //TODO: check response in error
        if (!is_numeric($min) || !is_numeric($max) || $min<0 || $max<0){
            return;
        }
        $recipe = new Recipe();
        $recipes = $recipe->searchByEnergyRange($min,$max);
        return $recipes;
    }

    public function show($id) {

        $Recipe = new Recipe();
        $recipeItem = $Recipe->find($id);

        $response = [
            "name" => $recipeItem->user->name,
            "user_id" => $recipeItem->user_id,
            "visibility" => $recipeItem->visibility,
            "energy_total" => $recipeItem->energy_total,
            "ingredients" => $recipeItem->ingredients
        ];

        return ["data"=>$response];
    }


}


