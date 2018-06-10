<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET"})
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('default/home.html.twig');
    }

    /**
     * @Route("/test", name="test")
     * @Method({"GET"})
     * @return Response
     */
    public function testAction(): Response
    {
        return $this->render('default/test.html.twig');
    }
}
