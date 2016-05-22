<?php

use GoCanada\NdbClient\NdbClient;
use GoCanada\NdbClient\NdbClientFactory;

class NdbClientFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function test_it_builds_from_env()
    {
        $factory   = new NdbClientFactory();
        $ndbClient = $factory->buildFromEnv();

        $ndbno = '28258-123123';
        $ndbClient->getFoodReport($ndbno);
    }
}