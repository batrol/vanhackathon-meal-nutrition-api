<?php
use GoCanada\Models\Recipe;
use GoCanada\Models\IngredientRecipe;

use GuzzleHttp\Client;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;


class RecipeControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function test_it_responses_all_recipe_nutrition_info()
    {
        $this->get('/api/v1/recipe/1/nutrition-info');
        $responseData = $this->getResponseData();
        $this->assertTrue(isset($responseData->nutrients));
        foreach($responseData->nutrients as $nutrient){
            $this->assertTrue(isset($nutrient->name));
            $this->assertTrue(isset($nutrient->value));
            $this->assertTrue(isset($nutrient->unit));
            $this->assertTrue(isset($nutrient->group));
        }

    }

    /**
     * @test
     */
    public function test_it_stores_a_new_recipe_and_its_ingredients()
    {
        $data = [
            'name' => 'Perfect Meal',
            'visibility' => 'PUBLIC',
        ];
        $expectedData = [
            'name' => 'Perfect Meal',
            'visibility' => 'PUBLIC',
        ];

        $this->post('api/v1/recipe', $data)
            ->seeStatusCode(\Illuminate\Http\Response::HTTP_CREATED)
            ->seeInDatabase('recipe', $expectedData);
    }

    /**
     * @test
     */
    public function test_it_fails_on_storing_a_new_recipe_with_a_wrong_visibility_option()
    {
        $data = [
            'name' => 'Perfect Meal',
            'visibility' => 'PUBLICC',
        ];
        $expectedData = [
            'name' => 'Perfect Meal',
            'visibility' => 'PUBLICC',
        ];

        $this->post('api/v1/recipe', $data)
            ->seeStatusCode(\Illuminate\Http\Response::HTTP_BAD_REQUEST)
            ->dontSeeInDatabase('recipe', $expectedData);
    }

    /**
     * @test
     */
    public function test_it_fails_on_storing_a_new_recipe_without_ingredients()
    {
        $data = [
            'name' => 'Perfect Meal',
            'visibility' => 'PUBLIC',
        ];
        $expectedData = [
            'name' => 'Perfect Meal',
            'visibility' => 'PUBLIC',
        ];

        $this->post('api/v1/recipe', $data)
            ->seeStatusCode(\Illuminate\Http\Response::HTTP_BAD_REQUEST)
            ->dontSeeInDatabase('recipe', $expectedData);
    }

    public function test_it_responses_all_recipe_show()
    {
        $this->get('/api/v1/recipe/2');
        $recipe = $this->getResponseData();
        $this->assertTrue(isset($recipe));
        $this->assertTrue(isset($recipe->name));
        $this->assertTrue(isset($recipe->user_id));
        $this->assertTrue(isset($recipe->visibility));
        $this->assertTrue(isset($recipe->energy_total));
        foreach($recipe->ingredients as $ingredient){
            $this->assertTrue(isset($ingredient->ndbno));
            $this->assertTrue(isset($ingredient->quantity));
        }
    }
}


