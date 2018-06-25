<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ListController
 * @Route("list")
 */
class ListController extends BwController
{
    /**
     * @Route("/", name="list_index")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(): \Symfony\Component\HttpFoundation\Response
    {
        $lists = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:GiftList')->findAll();

        return $this->render('AppBundle:list:index.html.twig', compact('lists'));
    }

    /**
     * @param Request $request
     * @Route("/{id}", name="list_show", requirements={"id": "\d+"})
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction(Request $request)
    {
        $id = $request->get('id');

        $list = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:GiftList')->findFullById($id);
        if (!$list) {
            throw $this->createNotFoundException();
        }

        $nextEventData = $this->container->get('bw.list_event_manager')->getNearestEventData($list);

        return $this->render('AppBundle:list:show.html.twig', compact('list', 'nextEventData'));
    }
}
