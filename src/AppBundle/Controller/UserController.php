<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class UserController
 * @Route("user")
 * @Security("has_role('ROLE_USER')")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user_home")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        return $this->render('AppBundle:user:index.html.twig');
    }

    /**
     * @Route("/change-options", name="user_change_options")
     * @Method({"GET"})
     */
    public function changeOptionsAction()
    {
        return $this->render('AppBundle:user:change_options.html.twig');
    }
}
