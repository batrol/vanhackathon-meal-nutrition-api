<?php

interface RecipeRepositoryInterface
{
    public function getIngredientsByRecipe($id);
    public function getNutritionalInformationByRecipe($id);
    
}