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
        $name = uniqid("Perfect meal", true);
        $data = [
            'user_id' => '1',
            'name' => $name,
            'visibility' => 'PUBLIC',
            'ingredients' => [
                [
                    'ndbno' => '01090',
                    'quantity' => '1',
                ],
            ],
        ];
        $expectedRecipeData = [
            'name' => $name,
            'visibility' => 'PUBLIC',
        ];

        $response = $this->post('api/v1/recipe', $data)
            ->seeStatusCode(\Illuminate\Http\Response::HTTP_CREATED)
            ->seeInDatabase('recipe', $expectedRecipeData);

        $expectedIngredientsData = [
            'recipe_id' => $response->getResponseData()->data->id,
            'ndbno' => '01090',
            'quantity' => '1',
        ];
        $response->seeInDatabase('ingredient_recipe', $expectedIngredientsData);
    }

    /**
     * @test
     */
    public function test_it_updates_an_existing_recipe_and_its_ingredients()
    {
        $name = uniqid("Perfect meal", true);
        $data = [
            'user_id' => '1',
            'name' => $name,
            'visibility' => 'PUBLIC',
            'ingredients' => [
                [
                    'ndbno' => '01090',
                    'quantity' => '1',
                ],
            ],
        ];
        $expectedRecipeData = [
            'name' => $name,
            'visibility' => 'PUBLIC',
        ];

        $expectedIngredientsData = [
            'recipe_id' => 1,
            'ndbno' => '01090',
            'quantity' => '1',
        ];

        $this->put('api/v1/recipe/1', $data)
            ->seeStatusCode(\Illuminate\Http\Response::HTTP_OK)
            ->seeInDatabase('recipe', $expectedRecipeData)
            ->seeInDatabase('ingredient_recipe', $expectedIngredientsData);
    }

    /**
     * @test
     */
    public function test_it_fails_on_storing_a_new_recipe_with_a_wrong_visibility_option()
    {
        $name = uniqid("Perfect meal", true);
        $data = [
            'user_id' => '1',
            'name' => $name,
            'visibility' => 'PUBLICC',
            'ingredients' => [
                [
                    'ndbno' => '01090',
                    'quantity' => '1',
                ],
            ],
        ];
        $expectedRecipeData = [
            'name' => $name,
            'visibility' => 'PUBLICC',
        ];

        $this->post('api/v1/recipe', $data)
            ->seeStatusCode(\Illuminate\Http\Response::HTTP_BAD_REQUEST)
            ->dontSeeInDatabase('recipe', $expectedRecipeData);
    }

    /**
     * @test
     */
    public function test_it_fails_on_storing_an_existing_recipe_with_a_wrong_visibility_option()
    {
        $name = uniqid("Perfect meal", true);
        $data = [
            'user_id' => '1',
            'name' => $name,
            'visibility' => 'PUBLICC',
            'ingredients' => [
                [
                    'ndbno' => '01090',
                    'quantity' => '1',
                ],
            ],
        ];
        $expectedRecipeData = [
            'name' => $name,
            'visibility' => 'PUBLICC',
        ];

        $this->put('api/v1/recipe/1', $data)
            ->seeStatusCode(\Illuminate\Http\Response::HTTP_BAD_REQUEST)
            ->dontSeeInDatabase('recipe', $expectedRecipeData);
    }

    /**
     * @test
     */
    public function test_it_fails_on_storing_a_new_recipe_without_ingredients()
    {
        $name = uniqid("Perfect meal", true);
        $data = [
            'user_id' => '1',
            'name' => $name,
            'visibility' => 'PUBLIC',
        ];
        $expectedRecipeData = [
            'name' => $name,
            'visibility' => 'PUBLIC',
        ];

        $this->post('api/v1/recipe', $data)
            ->seeStatusCode(\Illuminate\Http\Response::HTTP_BAD_REQUEST)
            ->dontSeeInDatabase('recipe', $expectedRecipeData);
    }

    /**
     * @test
     */
    public function test_it_fails_on_storing_an_existing_recipe_without_ingredients()
    {
        $name = uniqid("Perfect meal", true);
        $data = [
            'user_id' => '1',
            'name' => $name,
            'visibility' => 'PUBLIC',
        ];
        $expectedRecipeData = [
            'name' => $name,
            'visibility' => 'PUBLIC',
        ];

        $this->put('api/v1/recipe/1', $data)
            ->seeStatusCode(\Illuminate\Http\Response::HTTP_BAD_REQUEST)
            ->dontSeeInDatabase('recipe', $expectedRecipeData);
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


