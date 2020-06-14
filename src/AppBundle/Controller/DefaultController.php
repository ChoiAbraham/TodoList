<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class DefaultController extends Controller
{
    /** @var Environment  */
    private $environment;

    /*
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return new Response($this->environment->render('default/index.html.twig'));
    }
}
