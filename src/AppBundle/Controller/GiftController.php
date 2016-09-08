<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GiftController
 * @package AppBundle\Controller
 */
class GiftController extends Controller
{
    /**
     * @param Request $request
     * @Route("/gift/{id}", name="gift_show", requirements={"id": "\d+"})
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction(Request $request)
    {
        $id = $request->get('id');

        $gift = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Gift')->findFullById($id);
        if(!$gift) {
            throw $this->createNotFoundException();
        }
        return $this->render('AppBundle:gift:show.html.twig', compact('gift'));
    }
}
