<?php

use GoCanada\NdbClient\NdbClient;
use GoCanada\NdbClient\NdbClientFactory;

class NdbClientFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function test_it_builds_for_api_key()
    {
        $factory   = new NdbClientFactory();
        $ndbClient = $factory->build('asdasd');

        $this->assertEquals(NdbClient::class, get_class($ndbClient));
    }

    /**
     * @test
     */
    public function test_it_builds_from_env()
    {
        $original = env('NDB_API_KEY');

        putenv("NDB_API_KEY=123456");

        $factory   = new NdbClientFactory();
        $ndbClient = $factory->buildFromEnv();

        $this->assertEquals('123456', $ndbClient->getApiKey());

        putenv("NDB_API_KEY={$original}");
    }
}