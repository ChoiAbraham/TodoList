<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\TestDataFixtures;
use AppBundle\Entity\User;
use Tests\AppBundle\AbstractTestController;

class UserControllerTest extends AbstractTestController
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([
            TestDataFixtures::class
        ]);
    }

    public function testRedirectListUsersWithoutAuthentification()
    {
        $this->client->request('GET', '/users');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertHomepageRedirect($crawler);
    }

    public function testRedirectEditActionWithoutAuthentification()
    {
        $this->client->request('GET', '/users/create');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertHomepageRedirect($crawler);
    }

    public function testForbiddenListUsersAsUser()
    {
        $this->logUser();
        $this->client->request('GET', '/users');

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function assertHomepageRedirect($crawler)
    {
        $this->assertSame(1, $crawler->filter('html:contains("Nom d\'utilisateur :")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Mot de passe :")')->count());
    }

    public function testSuccessListUsersAsAdmin()
    {
        $this->logAdmin();
        $crawler = $this->client->request('GET', '/users');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Liste des utilisateurs")')->count());
    }

    public function testForbiddenCreateUserAsUser()
    {
        $this->logUser();

        $this->client->request('GET', '/users/create');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

    }

    public function testRedirectEditUserWithoutAuthentification()
    {
        $this->client->request('GET', '/users/create');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame(1, $crawler->filter('html:contains("Se connecter")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Mot de passe :")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Nom d\'utilisateur :")')->count());
    }

    public function testSuccessAccessPageCreateUserAsAdmin()
    {
        $this->logAdmin();

        $crawler = $this->client->request('GET', '/users/create');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Rôle Administrateur")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Créer un utilisateur")')->count());
    }

    public function testSuccessCreateUserAsAdmin()
    {
        $this->logAdmin();

        $crawler = $this->client->request('POST', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();

        $form['user[username]'] = 'test';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'test@gmail.com';
        $form['user[roles]'] = ['ROLE_ADMIN'];

        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été ajouté.")')->count());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccessEditUserPageFormAsAdmin()
    {
        $this->logAdmin();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'username1']);

        $crawler = $this->client->request('GET', '/users/'.$user->getId().'/edit');
        $this->assertSame(1, $crawler->filter('html:contains("Mot de passe")')->count());
        $this->assertSame(1, $crawler->filter('html:contains("Adresse email")')->count());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccessEditUserAsAdmin()
    {
        $this->logAdmin();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'username1']);

        $crawler = $this->client->request('POST', '/users/'.$user->getId().'/edit');

        $form = $crawler->selectButton('Modifier')->form();

        $form['user[username]'] = 'test';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'test@gmail.com';
        $form['user[roles]'] = ['ROLE_ADMIN'];

        $this->client->submit($form);

        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("Superbe ! L\'utilisateur a bien été modifié")')->count());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}