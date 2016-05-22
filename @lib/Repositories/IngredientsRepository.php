<?php namespace GoCanada\Repositories;

use GoCanada\NdbClient\NdbClientFactory;
use GoCanada\Popos\Nutrient;

class IngredientsRepository implements IngredientsRepositoryInterface
{

    public function getNutrientsByIngredient($ndbno)
    {
        $factory   = new NdbClientFactory();
        $ndbClient = $factory->buildFromEnv();

        $ndbClient->getFoodReport($ndbno);

        $nutrients = $ndbClient->getFoodReport($ndbno)->report->food->nutrients;
        $popos     = [];

        foreach ($nutrients as $nutrient) {
            $popo = new Nutrient();
            $popo->setId($nutrient->nutrient_id);
            $popo->setName($nutrient->name);
            $popo->setGroup($nutrient->group);
            $popo->setValue($nutrient->value);
            $popo->setUnit($nutrient->unit);

            $popos[] = $popo;
        }

        return $popos;
    }
}