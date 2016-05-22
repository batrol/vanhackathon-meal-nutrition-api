<?php


namespace GoCanada\Repositories;
use GoCanada\Models\Recipe;
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

}