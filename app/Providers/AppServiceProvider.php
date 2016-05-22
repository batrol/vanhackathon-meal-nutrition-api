<?php

namespace App\Providers;

use GoCanada\Repositories\IngredientsCacheDecoratorRepository;
use GoCanada\Repositories\IngredientsRepositoryInterface;
use GoCanada\Repositories\RecipeRepositoryInterface;
use GoCanada\Repositories\RecipeRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IngredientsRepositoryInterface::class, IngredientsCacheDecoratorRepository::class);
        $this->app->bind(RecipeRepositoryInterface::class, RecipeRepository::class);

    }
}
