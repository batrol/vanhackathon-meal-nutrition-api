<?php namespace GoCanada\Repository;

class IngredientsRepository implements IngredientsRepositoryInterface
{

    public function getNutrientsByIngredient($ndbno)
    {
        // Consumes the USDA api that contains nutrition information about each ingredient.
        $apiKey= 'IlAoU2IJI9TWWN7wmupWrZFwOfbyjOwNmTS2eZsy';
        $apiUrl = 'http://api.nal.usda.gov/ndb/reports/?ndbno='.$ndbno.'&type=f&format=json&api_key='.$apiKey;
        $client = new Client();
        $response = $client->request('GET', $apiUrl);
        $responseBody =  $response->getBody();

        //TODO: check
        if ($response->getStatusCode() != 200){
            return $this->returnWithError('conexao falhou', 400);
//                $returnData = array(
//                    'status' => 'error',
//                    'message' => 'No Api Response'
//                );
//                return response()->json($returnData, 500);
        }
        $apiIngredient = json_decode($responseBody, true);

        // Iterates over nutrients to find nutrients information and fills array that will be returned.
        $nutrients = $apiIngredient['report']['food']['nutrients'];
    }
}