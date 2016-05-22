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

    // function that search recipe by name part(insentitive case)
    function findByName($name)
    {
        $name = strtolower($name);
        $recipe = $this->makeModel();
        return $recipe->whereRaw("LOWER(name) like '%".$name."%'")->with('ingredients')->get();
    }

}