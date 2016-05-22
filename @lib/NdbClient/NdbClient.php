<?php namespace GoCanada\NdbClient;

use GuzzleHttp\Client;

class NdbClient
{
    const BASE_URL = 'http://api.nal.usda.gov/ndb/';

    protected $client;
    protected $apiKey;

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     * @return NdbClient
     */
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param mixed $apiKey
     * @return NdbClient
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function __construct($apiKey)
    {
        $this->client = new Client();
        $this->apiKey = $apiKey;
    }

    public function getFoodReport($ndbno)
    {
        $response     = $this->client->get(static::BASE_URL . '?ndbno=' . $ndbno . '&type=f&format=json&api_key=' . $this->getApiKey());
        $responseBody =  $response->getBody();

        return dd(json_decode($responseBody));
    }
}