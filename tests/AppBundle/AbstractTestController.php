<?php

namespace Tests\AppBundle;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AbstractTestController extends WebTestCase
{
    protected $client;

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function setUp():void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
        $schemaTool->createSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
    }

    protected function logUser()
    {
        $session = $this->client->getContainer()->get('session');
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'username1']);

        $token = new UsernamePasswordToken($user, null, 'main', ['ROLE_USER']);
        $session->set('_security_'.'main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    protected function logAdmin()
    {
        $session = $this->client->getContainer()->get('session');
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>'admin']);

        $token = new UsernamePasswordToken($user, null, 'main', ['ROLE_ADMIN']);
        $session->set('_security_'.'main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}