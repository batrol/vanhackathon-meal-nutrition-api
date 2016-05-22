<?php namespace GoCanada\Repositories;

interface IngredientsRepositoryInterface
{
    public function getNutrientsByIngredient($ndbno);
}