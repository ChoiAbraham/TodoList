<?php

namespace Tests\AppBundle\Form;

use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{

    public function testFormFields()
    {
        $formData = [
            'title' => 'titre',
            'content' => 'Content',
        ];

        $taskToCompare = $this->createMock(Task::class);
        $form = $this->factory->create(TaskType::class, $taskToCompare);

        $task = $this->createMock(Task::class);

        $task->setTitle('titre');
        $task->setContent('Content');

        $form->submit($formData);

        $this->assertTrue($form->isValid());
        $this->assertEquals($task, $taskToCompare);
    }
}