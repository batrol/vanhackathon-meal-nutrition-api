<?php

use GoCanada\NdbClient\NdbClient;
use GoCanada\NdbClient\NdbClientFactory;

class NdbClientTest extends TestCase
{

    /**
     * @test
     */
    public function it_get_response()
    {
        $factory   = new NdbClientFactory();
        $ndbClient = $factory->buildFromEnv();

        $ndbno = '28258';
        $ndbClient->getFoodReport($ndbno);
    }
}