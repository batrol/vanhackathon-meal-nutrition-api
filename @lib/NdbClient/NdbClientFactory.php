<?php namespace GoCanada\NdbClient;

class NdbClientFactory
{
    public function build($apiKey)
    {
        $client = new NdbClient($apiKey);
        return $client;
    }

    public function buildFromEnv()
    {
        return $this->build(env('NDB_API_KEY'));
    }
}