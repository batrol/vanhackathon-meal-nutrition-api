<?php

namespace GoCanada\Repositories;


use Prettus\Repository\Contracts\RepositoryInterface;

interface RecipeRepositoryInterface extends RepositoryInterface
{
    public function sumNutritionInfo($ingredients, IngredientsRepositoryInterface $ingredientsRepo);
}