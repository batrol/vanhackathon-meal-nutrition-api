<?php

namespace GoCanada\Repositories;


use Prettus\Repository\Contracts\RepositoryInterface;

interface RecipeRepositoryInterface extends RepositoryInterface
{
    // function that search recipe by name part(insentitive case)
    function findByName($name);



}