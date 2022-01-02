<?php

namespace BestWishes\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        return $this->render('default/home.html.twig');
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(): Response
    {
        return $this->render('default/test.html.twig');
    }
}
