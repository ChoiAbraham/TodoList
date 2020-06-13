<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use AppBundle\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class TaskController extends Controller
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
     * @param FlashBagInterface $flashBag
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(Environment $environment, TaskRepository $taskRepository, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, FlashBagInterface $flashBag, TokenStorageInterface $tokenStorage)
    {
        $this->environment = $environment;
        $this->taskRepository = $taskRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction()
    {
        $tasks = $this->taskRepository->findAll();

        return new Response($this->environment->render(
            'task/list.html.twig',
            [
                'tasks' => $tasks
            ]
        ));
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->formFactory->create(TaskType::class, $task)->handleRequest($request);

        $user =  $this->tokenStorage->getToken()->getUser();

        if ($form->isValid() && $form->isSubmitted()) {

            $task->setUser($user);
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
     */
    public function editAction(Task $task, Request $request)
    {
        $form = $this->formFactory->create(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
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
     */
    public function toggleTaskAction(Task $task)
    {
        $task->toggle(!$task->isDone());
        $this->entityManager->flush();

        $this->flashBag->add('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return new RedirectResponse($this->urlGenerator->generate('task_list'));
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task)
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'La tâche a bien été supprimée.');

        return new RedirectResponse($this->urlGenerator->generate('task_list'));
    }
}
