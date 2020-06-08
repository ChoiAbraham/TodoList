<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AbstractTestController extends WebTestCase
{
    protected $client;

    public function setUp():void
    {
        $this->client = static::createClient();
    }
}