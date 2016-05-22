<?php namespace GoCanada\Repositories;

use GoCanada\NdbClient\NdbClientFactory;
use GoCanada\Popos\Nutrient;
use Cache;

class IngredientsCacheDecoratorRepository extends IngredientsRepository
{

    public function getNutrientsByIngredient($ndbno)
    {
        $key = 'getNutrientsByIngredient_' . $ndbno;
        if ($data = Cache::get($key)) {
            return $data;
        }

        $popos = parent::getNutrientsByIngredient($ndbno);

        Cache::forever($key, $popos);

        return $popos;
    }
}