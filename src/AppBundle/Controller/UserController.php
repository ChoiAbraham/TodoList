<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

class UserController
{
    /** @var Environment */
    private $environment;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var AuthorizationCheckerInterface */
    private $authChecker;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var UserRepository */
    private $userRepository;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /** @var ValidatorInterface */
    private $validator;

    /**
     * UserController constructor.
     * @param Environment $environment
     * @param FlashBagInterface $flashBag
     * @param EntityManagerInterface $entityManager
     * @param AuthorizationCheckerInterface $authChecker
     * @param UrlGeneratorInterface $urlGenerator
     * @param FormFactoryInterface $formFactory
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $encoder
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator, Environment $environment, FlashBagInterface $flashBag, EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authChecker, UrlGeneratorInterface $urlGenerator, FormFactoryInterface $formFactory, UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $this->environment = $environment;
        $this->flashBag = $flashBag;
        $this->entityManager = $entityManager;
        $this->authChecker = $authChecker;
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
        $this->validator = $validator;
    }

    /**
     * @Route("/users", name="user_list")
     */
    public function listAction()
    {
        return new Response($this->environment->render(
            'user/list.html.twig',
            [
                    'users' => $this->userRepository->findAll()
                ]
        ));
    }

    /**
     * @Route("/users/create", name="user_create")
     * @IsGranted("ROLE_ADMIN")
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->formFactory->create(UserType::class, $user,
            [
                'validation_groups' => ['Default', 'Registration'],
            ]
        );

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $password = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flashBag->add('success', "L'utilisateur a bien été ajouté.");

            return new RedirectResponse($this->urlGenerator->generate('user_list'));
        }

        return new Response(
            $this->environment->render(
                'user/create.html.twig',
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editAction(User $user, Request $request)
    {
        $form = $this->formFactory->create(UserType::class, $user,
            [
                'validation_groups' => ['Default'],
            ]
        );

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $password = $form->getData()->getPassword();
            if ($password !== null) {
                $password = $this->encoder->encodePassword($user, $user->getPassword());

                $user->setPassword($password);
                $this->entityManager->flush();
            }

            $this->flashBag->add('success', "L'utilisateur a bien été modifié");

            return new RedirectResponse($this->urlGenerator->generate('user_list'));
        }

        return new Response(
            $this->environment->render(
                'user/edit.html.twig',
                [
                    'form' => $form->createView(), 'user' => $user
                ]
            )
        );
    }
}
