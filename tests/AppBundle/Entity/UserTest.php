<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class UserTest extends KernelTestCase
{
    protected function setUp():void
    {
        parent::setUp();
    }

    public function createUserEntity()
    {
        $user = new User();
        $user->setUsername('username');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);
        $user->setEmail('email@gmail.com');

        return $user;
    }

    public function testValidUserEntity()
    {
        /** @var User $user */
        $user = $this->createUserEntity();
        $this->assertHasErrors($user, 0);
    }

    public function testInvalidBlankUsername()
    {
        /** @var User $user */
        $user = $this->createUserEntity()->setUsername('');
        $this->assertHasErrors($user, 1);
    }

    public function testInvalidBlankEmail()
    {
        /** @var User $user */
        $user = $this->createUserEntity()->setEmail('');
        $this->assertHasErrors($user, 1);
    }

    public function testGetRoles()
    {
        /** @var User $user */
        $user = $this->createUserEntity();

        $user->setRoles(['ROLE_USER']);
        static::assertSame(['ROLE_USER'], $user->getRoles());
    }

    public function assertHasErrors($entity, $number)
    {
        self::bootKernel();
        $errors = self::$kernel->getContainer()->get('validator')->validate($entity);

        $messages = [];
        /** @var ConstraintViolation $error */
        foreach($errors as $error)
        {
            $messages[] =  $error->getPropertyPath() . ' -> ' . $error->getMessage();
        }

        static::assertCount($number, $errors, implode(', ', $messages));
    }
}