<?php

namespace BestWishes\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'homepage')]
    public function index(): Response
    {
        return $this->render('default/home.html.twig');
    }
}
