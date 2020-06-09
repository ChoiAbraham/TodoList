<?php


namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\TestDataFixtures;
use Tests\AppBundle\AbstractTestController;

class TaskControllerTest extends AbstractTestController
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([
            TestDataFixtures::class
        ]);
    }

    public function testLoginUser()
    {
        $this->logUser();

        $crawler = $this->client->request('GET', '/');

        $this->assertSame(1, $crawler->filter('html:contains("Bienvenue sur Todo List")')->count());
    }
}