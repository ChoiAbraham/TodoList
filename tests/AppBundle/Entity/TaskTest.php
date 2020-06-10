<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class TaskTest extends KernelTestCase
{
    /** @var Task */
    private $task;

    protected function setUp():void
    {
        parent::setUp();
        $this->task = new Task();
    }

    public function testValidTaskEntity()
    {
        $this->task->setTitle('task_title');
        $this->task->setContent('content of the task');
        $this->task->setCreatedAt(new \DateTime());

        $this->assertHasErrors($this->task, 0);
    }

    public function testInvalidBlankTitle()
    {
        $this->task->setContent('content');
        $this->task->setTitle('');
        $this->assertHasErrors($this->task, 1);
    }

    public function testInvalidBlankContent()
    {
        $this->task->setContent('');
        $this->task->setTitle('title');
        $this->assertHasErrors($this->task, 1);
    }

    public function testIsDone()
    {
        $this->task->toggle(false);

        static::assertSame(false, $this->task->isDone());
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