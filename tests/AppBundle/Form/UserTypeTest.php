<?php

namespace Tests\AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserTypeTest extends TypeTestCase
{
    public function testSubmitValidDataUser()
    {
        $formData = [
            'username' => 'username',
            'password' => [
                'first' => 'password',
                'second' => 'password',
            ],
            'email' => 'email@gmail.com',
            'roles' => ['ROLE_USER']
        ];

        $userForm = $this->createMock(User::class);
        $form = $this->factory->create(UserType::class, $userForm);

        $form->submit($formData);

        $user = $this->createMock(User::class);

        $user->setUsername('username');
        $user->setPassword('password');
        $user->setEmail('email@gmail.com');
        $user->setRoles(['ROLE_USER']);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($user, $form->getData());
    }
}