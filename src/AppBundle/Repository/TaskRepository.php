<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository
{
    /**
     * TaskRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function getAdminTasks(User $user)
    {
        return $this->findBy(
            [
                'user' => [
                    null,
                    $user
                ],
                'isAnonymous' => true
            ]);
    }

    public function getUserTasks(User $user)
    {
        return $this->findBy(['user' => $user]);
    }
}
