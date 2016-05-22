<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function getResponseData()
    {
        return $respData = json_decode($this->response->getContent());
    }

    protected function seeJsonHeader()
    {
        $actual   = $this->response->headers->get('content-type');

        $this->assertTrue((bool) preg_match('/^application\/json/', $actual));

        return $this;
    }
}
