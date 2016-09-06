<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 * @package AppBundle\Controller
 */
class CategoryController extends Controller
{
    /**
     * @param Request $request
     * @Route("/cat/{id}", name="category_show", requirements={"id": "\d+"})
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction(Request $request)
    {
        $id = $request->get('id');

        $category = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Category')->find($id);
        if(!$category) {
            throw $this->createNotFoundException();
        }
        return $this->render('AppBundle:category:show.html.twig', compact('category'));
    }
}
