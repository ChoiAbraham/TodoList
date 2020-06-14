<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\TestDataFixtures;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
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

    public function testSuccessListTasksAsUser()
    {
        $this->logUser();
        $crawler = $this->client->request('GET', '/tasks');

        $this->assertSame(1, $crawler->filter('html:contains("Créer une tâche")')->count());
    }

    public function testFailListTasksWithoutAuthentification()
    {
        $this->client->request('GET', '/tasks');
        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateTaskAsUser()
    {
        $this->logUser();
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();

        $form['task[title]'] = 'warning MUST DO TODAY';
        $form['task[content]'] = 'you must do this and that, remember';

        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame(1, $crawler->filter('html:contains("La tâche a été bien été ajoutée.")')->count());
    }

    public function testCreateTaskFormAsUser()
    {
        $this->logUser();

        $crawler = $this->client->request('GET', '/tasks/create');

        $this->assertSame(1, $crawler->filter('html:contains("Retour à la liste des tâches")')->count());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testToggleStatutAsUser()
    {
        $this->logUser();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'username1']);
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['user' => $user->getId()]);
        $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');

        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("a bien été marquée comme")')->count());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testEditTaskAsUser()
    {
        $this->logUser();

        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'username1']);

        $taskToEdit = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'title1', 'user' => $user->getId()]);

        $crawler = $this->client->request('POST', '/tasks/' . $taskToEdit->getId() . '/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'newtitle';
        $form['task[content]'] = 'newcontent';
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        $this->assertSame(1, $crawler->filter('html:contains("La tâche a bien été modifiée.")')->count());
    }

    public function testSuccessDeleteTaskAsUser()
    {
        $this->logUser();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'username1']);
        $taskToDelete = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'title1', 'user' => $user->getId()]);
        $this->client->request('GET', '/tasks/'.$taskToDelete->getId().'/delete');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("La tâche a bien été supprimée.")')->count());
    }

    public function testSuccessDeleteAnonymousTaskAsAdmin()
    {
        $this->logAdmin();
        $taskToDelete = $this->entityManager->getRepository(Task::class)->findOneBy(['isAnonymous' => true]);

        $this->client->request('DELETE', '/tasks/'.$taskToDelete->getId().'/delete');

        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("La tâche a bien été supprimée.")')->count());
    }

    public function testFailureDeleteTaskOfUser1AsUser2()
    {
        $this->logAdmin();

        $crawler = $this->client->request('POST', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();

        $form['task[title]'] = 'TASK';
        $form['task[content]'] = 'CONTENT';

        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->request('GET', '/logout');

        $taskToDelete = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'TASK']);

        $this->logUser();
        $this->client->request('DELETE', '/tasks/'.$taskToDelete->getId().'/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccessListTasksAsAdmin()
    {
        $this->logAdmin();

        $crawler = $this->client->request('GET', '/tasks');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}