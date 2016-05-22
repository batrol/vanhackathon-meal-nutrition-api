<?php namespace GoCanada\Repository;

interface IngredientsRepositoryInterface
{
    public function getNutrientsByIngredient($ndbno);
}