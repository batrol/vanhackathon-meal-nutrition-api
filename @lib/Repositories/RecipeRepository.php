<?php namespace GoCanada\Repositories;

use GoCanada\Models\Recipe;

class RecipeRepository implements RecipeRepositoryInterface
{
    public function getIngredientsByRecipe($id){

    }

    public function getNutritionalInformationByRecipe($id){

    }

    public function getRecipe($id = null){
        if ($id){
            return Recipe::findOrFail($id);
        }
        return new Recipe();
    }
}