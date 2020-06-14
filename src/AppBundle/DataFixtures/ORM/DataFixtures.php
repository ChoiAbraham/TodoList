<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DataFixtures extends AbstractFixture implements FixtureGroupInterface
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        //tasks without users (anonymous)
        for ($i = 0; $i <= 3; $i++) {
            $task = new Task();

            $task->setTitle($faker->title);
            $task->setContent($faker->text);
            $task->setCreatedAt(new \DateTime());

            $task->setIsAnonymous(true);

            $manager->persist($task);
        }

        //tasks with users
        for ($i = 0; $i <= 5; $i++) {
            $user = new User();

            $user->setEmail($faker->email);
            $user->setUsername($faker->userName);
            $user->setPassword($faker->password);
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }

        for ($i = 0; $i <= 3; $i++) {
            $task = new Task();

            $task->setTitle($faker->title);
            $task->setContent($faker->text);
            $task->setCreatedAt(new \DateTime());

            $task->setUser($user);
            $task->setIsAnonymous(false);

            $manager->persist($task);
        }

        //task with admin
        $userAdmin = new User();
        $userAdmin->setEmail('admin@gmail.com');
        $userAdmin->setUsername('admin');

        $password = $this->encoder->encodePassword($userAdmin, 'admin');
        $userAdmin->setPassword($password);
        $userAdmin->setRoles(['ROLE_ADMIN']);

        $manager->persist($userAdmin);

        $task = new Task();
        $task->setTitle('Title');
        $task->setContent('content_admin');
        $task->setCreatedAt(new \DateTime());
        $task->setUser($userAdmin);
        $task->setIsAnonymous(false);

        $manager->persist($task);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['data'];
    }
}