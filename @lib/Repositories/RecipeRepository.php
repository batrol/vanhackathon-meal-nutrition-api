<?php


namespace GoCanada\Repositories;
use GoCanada\Models\Recipe;
use Prettus\Repository\Eloquent\BaseRepository;

class RecipeRepository extends BaseRepository  implements RecipeRepositoryInterface
{
    protected $fieldSearchable = [
        'id',
        'name'=>'like',
        'energy_total'

    ];

    public function model()
    {
        return Recipe::class;
    }
/*
    public function searchByUser($userId){
        $recipe = $this->model;
        return $recipe::where("user_id",[$userId])->with('ingredients')->get();
    }

    public function searchByName($name){
        $name = strtolower($name);
        $recipe = $this->model;
        return $recipe::whereRaw("LOWER(name) like '%".$name."%'")->with('ingredients')->get();
    }

    public function searchByEnergyMin($min){
        $recipe = $this->model;
        return $recipe::whereRaw("energy_total >= $min ")->with('ingredients')->get();
    }

    public function searchByEnergyMax($max){
        $recipe = $this->model;
        return $recipe::whereRaw("energy_total <= $max ")->with('ingredients')->get();
    }

    public function searchByEnergyRange($min,$max){
        $recipe = $this->model;
        return $recipe::whereRaw("energy_total between $min and $max ")->with('ingredients')->get();
    }
*/
}