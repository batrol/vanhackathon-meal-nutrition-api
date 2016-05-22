<?php
namespace GoCanada\Repositories;

use GoCanada\Models\Recipe;
use GoCanada\Popos\Nutrient;
use Prettus\Repository\Eloquent\BaseRepository;

class RecipeRepository extends BaseRepository  implements RecipeRepositoryInterface
{

    protected $fieldSearchable = [
        'id',
        'name' => 'like',
        'energy_total'

    ];

    public function model()
    {
        return Recipe::class;
    }

    // function that search recipe by name part(insentitive case)
    function findByName($name)
    {
        $name = strtolower($name);
        $recipe = $this->makeModel();
        return $recipe->whereRaw("LOWER(name) like '%".$name."%'")->with('ingredients')->get();
    }

    public function sumNutritionInfo($ingredients, IngredientsRepositoryInterface $ingredientsRepo){
        $ingredientsNutritionInfo = [];
        // Iterates over ingredients to fill the returning array.
        foreach($ingredients as $ingredient){

            // Get the ingredient identifier and quantity saved.
            $ndbno    = $ingredient->ndbno;
            $quantity = $ingredient->quantity;

            //TODO:COMMENT
            $nutrients = $ingredientsRepo->getNutrientsByIngredient($ndbno);

            /** @var Nutrient $nutrient */
            foreach ($nutrients as $nutrient) {

                $nutrientId = $nutrient->getId();

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

        return $ingredientsNutritionInfo;
    }
}