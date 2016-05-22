<?php

namespace GoCanada\Repositories;

use GoCanada\Models\Recipe;
use Cache;

class RecipeCacheDecoratorRepository extends RecipeRepository
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