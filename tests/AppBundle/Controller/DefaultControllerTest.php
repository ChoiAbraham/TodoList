<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\AbstractTestController;

class DefaultControllerTest extends AbstractTestController
{
    public function testIndex()
    {
        $this->client->request('GET', '/');

        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertContains('Se connecter', $crawler->filter('.btn.btn-success')->text());
    }
}
