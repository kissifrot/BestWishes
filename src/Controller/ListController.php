<?php

namespace BestWishes\Controller;


use BestWishes\Entity\GiftList;
use BestWishes\Manager\ListEventManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ListController
 * @Route("list")
 */
class ListController extends AbstractController
{
    private $listEventManager;

    public function __construct(ListEventManager $listEventManager)
    {
        $this->listEventManager = $listEventManager;
    }

    /**
     * @Route("/", name="list_index")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        $lists = $this->getDoctrine()->getManager()->getRepository(GiftList::class)->findAll();

        return $this->render('list/index.html.twig', compact('lists'));
    }

    /**
     * @param Request $request
     * @Route("/{id}", name="list_show", requirements={"id": "\d+"})
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request)
    {
        $id = $request->attributes->getInt('id');
        $list = $this->isGranted('IS_AUTHENTICATED_REMEMBERED')
            ? $this->getDoctrine()->getManager()->getRepository(GiftList::class)->findFullById($id)
            : $this->getDoctrine()->getManager()->getRepository(GiftList::class)->findFullSurpriseExcludedById($id)
        ;
        if (!$list) {
            throw $this->createNotFoundException();
        }
        $nextEventData = $this->listEventManager->getNearestEventData($list);

        return $this->render('list/show.html.twig', compact('list', 'nextEventData'));
    }
}
