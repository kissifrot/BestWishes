<?php

namespace BestWishes\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET"})
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('default/home.html.twig');
    }

    /**
     * @Route("/test", name="test")
     * @Method({"GET"})
     * @return Response
     */
    public function test(): Response
    {
        return $this->render('default/test.html.twig');
    }
}
