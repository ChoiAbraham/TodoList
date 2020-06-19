<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Form\TaskType;
use AppBundle\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

class TaskController
{

    /** @var Environment */
    private $environment;

    /** @var TaskRepository */
    private $taskRepository;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * TaskController constructor.
     * @param Environment $environment
     * @param TaskRepository $taskRepository
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param FlashBagInterface $flashBag
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(Environment $environment, TaskRepository $taskRepository, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, AuthorizationCheckerInterface $authorizationChecker, FlashBagInterface $flashBag, TokenStorageInterface $tokenStorage)
    {
        $this->environment = $environment;
        $this->taskRepository = $taskRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->authorizationChecker = $authorizationChecker;
        $this->flashBag = $flashBag;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/tasks", name="task_list")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function listAction()
    {
        /** @var User $user */
        $user =  $this->tokenStorage->getToken()->getUser();

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $tasks = $this->taskRepository->getAdminTasks($user);
        } else {
            $tasks = $this->taskRepository->getUserTasks($user);
        }

        return new Response($this->environment->render(
            'task/list.html.twig',
            [
                'tasks' => $tasks
            ]
        ));
    }

    /**
     * @Route("/tasks/create", name="task_create")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->formFactory->create(TaskType::class, $task)->handleRequest($request);

        $user =  $this->tokenStorage->getToken()->getUser();

        if ($form->isValid() && $form->isSubmitted()) {

            $task->setUser($user);
            $task->setIsAnonymous(false);

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'La tâche a été bien été ajoutée.');

            return new RedirectResponse($this->urlGenerator->generate('task_list'));
        }

        return new Response($this->environment->render(
            'task/create.html.twig',
            [
                'form' => $form->createView()
            ]
        ));
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function editAction(Task $task, Request $request)
    {
        $form = $this->formFactory->create(TaskType::class, $task);
        $form->handleRequest($request);

        if ($this->checkRights($task) && $form->isValid() && $form->isSubmitted()) {
            $this->entityManager->flush();

            $this->flashBag->add('success', 'La tâche a bien été modifiée.');

            return new RedirectResponse($this->urlGenerator->generate('task_list'));
        }

        return new Response($this->environment->render(
            'task/edit.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        ));
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function toggleTaskAction(Task $task)
    {
        if($this->checkRights($task)) {
            $task->toggle(!$task->isDone());
            $this->entityManager->flush();

            $this->flashBag->add('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
        }

        return new RedirectResponse($this->urlGenerator->generate('task_list'));
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function deleteTaskAction(Task $task)
    {
        if($this->checkRights($task)) {
            $this->entityManager->remove($task);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'La tâche a bien été supprimée.');
        }

        return new RedirectResponse($this->urlGenerator->generate('task_list'));
    }

    /**
     * Grant Admin Rights to Anonymous Tasks
     * Grant Users/Admins Rights to their Tasks
     * @param Task $task
     * @return bool
     */
    private function checkRights(Task $task): bool
    {
        if ($task->getIsAnonymous()) {
            // Task Has No User (isAnonymous), so Users cannot But Admin Can (return true)
            if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                return true;
            }
        }

        // tasks are not anonymous, check if the User of the task match the User connected
        if ($task->getUser() == $this->tokenStorage->getToken()->getUser()) {
            return true;
        }

        return false;
    }
}
