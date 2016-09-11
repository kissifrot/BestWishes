<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Gift;
use AppBundle\Form\Type\GiftType;
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

    /**
     * @param Request $request
     * @Route("/gift/create/{catId}", name="gift_create", requirements={"catId": "\d+"})
     * @Method({"GET", "POST"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $catId = $request->get('catId');
        $relatedCategory = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Category')->find($catId);
        if (!$relatedCategory) {
            throw $this->createNotFoundException();
        }
        $isSurprise = boolval($request->get('surprise', false));

        $gift = new Gift();
        $gift->setSurprise($isSurprise);
        $gift->setCategory($relatedCategory);

        $form = $this->createForm(GiftType::class, $gift);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($gift);
            $em->flush();

            $this->addFlash('notice', sprintf('Gift ""%s added',$gift->getName()));

            return $this->redirectToRoute('category_show', ['id' => $relatedCategory->getId()]);
        }

        return $this->render('AppBundle:gift:create.html.twig', ['form' => $form->createView(), 'isSurprise' => $isSurprise]);
    }
}
