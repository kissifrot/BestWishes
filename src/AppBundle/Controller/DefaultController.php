<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        return $this->render('default/home.html.twig');
    }

    /**
     * @Route("/test", name="test")
     * @Method({"GET"})
     */
    public function testAction()
    {
        return $this->render('default/test.html.twig');
    }
}
