<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class TestDataFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i <= 5; $i++) {
            $user = new User();

            $user->setEmail('email.' . $i . '@gmail.com');
            $user->setUsername('username' . $i);
            $user->setPassword('password' . $i);
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);

            for ($y = 0; $y <= 3; $y++) {
                $task = new Task();

                $task->setTitle('title' . $y);
                $task->setContent($faker->text);
                $task->setCreatedAt(new \DateTime());

                $task->setUser($user);

                $manager->persist($task);
            }
        }

        $manager->flush();
    }
}