<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Category;
use AppBundle\Entity\Gift;
use AppBundle\Event\GiftCreatedEvent;
use AppBundle\Event\GiftDeletedEvent;
use AppBundle\Event\GiftEditedEvent;
use AppBundle\Event\GiftPurchasedEvent;
use AppBundle\Form\Type\GiftType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class GiftController
 * @Route("gift")
 */
class GiftController extends BwController
{
    /**
     * @param Request $request
     * @Route("/{id}", name="gift_show", requirements={"id": "\d+"})
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction(Request $request)
    {
        $id = $request->get('id');

        $gift = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Gift')->findFullById($id);
        if (!$gift) {
            throw $this->createNotFoundException();
        }

        $deleteForm = $this->createSimpleActionForm($gift, 'delete')->createView();
        $markReceivedForm = $this->createSimpleActionForm($gift, 'mark_received')->createView();
        $markBoughtForm = $this->createPurchaseForm($gift)->createView();

        return $this->render('AppBundle:gift:show.html.twig',
            compact('gift', 'deleteForm', 'markReceivedForm', 'markBoughtForm'));
    }

    /**
     * @param Request  $request
     * @param Category $category
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/create/{catId}", name="gift_create", requirements={"catId": "\d+"})
     * @ParamConverter("category", options={"id" = "catId"})
     * @Method({"GET", "POST"})
     *
     */
    public function createAction(Request $request, Category $category)
    {
        $isSurprise = boolval($request->get('surprise', false));

        // Access control
        $this->checkAccess(
            $isSurprise ? ['EDIT', 'SURPRISE_ADD'] : ['OWNER', 'EDIT'],
            $category->getList()
        );

        $gift = new Gift();
        $gift->setSurprise($isSurprise);
        $gift->setCategory($category);

        $form = $this->createForm(GiftType::class, $gift);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($gift);
            $em->flush();

            // Dispatch the creation event
            $event = new GiftCreatedEvent($gift, $this->getUser());
            $this->get('event_dispatcher')->dispatch(GiftCreatedEvent::NAME, $event);

            $this->addFlash('notice', sprintf('Gift "%s" added', $gift->getName()));

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return $this->render('AppBundle:gift:create.html.twig',
            ['form' => $form->createView(), 'category' => $category, 'isSurprise' => $isSurprise]);
    }

    /**
     * @param Request $request
     * @param Gift    $gift
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("{id}/edit", name="gift_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     *
     */
    public function editAction(Request $request, Gift $gift)
    {
        // Access control
        $this->checkAccess(
            $gift->isSurprise() ? ['OWNER', 'EDIT', 'SURPRISE_ADD'] : ['OWNER', 'EDIT'],
            $gift->getCategory()->getList()
        );

        $originGift = clone $gift;
        $form = $this->createForm(GiftType::class, $gift);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($gift);
            $em->flush();

            // Dispatch the edition event
            $event = new GiftEditedEvent($originGift, $gift, $this->getUser());
            $this->get('event_dispatcher')->dispatch(GiftEditedEvent::NAME, $event);

            $this->addFlash('notice', sprintf('Gift "%s" updated', $gift->getName()));

            return $this->redirectToRoute('gift_show', ['id' => $gift->getId()]);
        }

        return $this->render('AppBundle:gift:edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param Gift    $gift
     * @Route("/{id}", name="gift_delete", requirements={"id": "\d+"})
     * @Method({"DELETE"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, Gift $gift)
    {
        // Access control
        $this->checkAccess(
            $gift->isSurprise() ? ['OWNER', 'EDIT', 'SURPRISE_ADD'] : ['OWNER', 'EDIT'],
            $gift->getCategory()->getList()
        );

        $form = $this->createSimpleActionForm($gift, 'delete');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $deletedGift = clone $gift;
            $em->remove($gift);
            $em->flush();

            // Dispatch the deletion event
            $event = new GiftDeletedEvent($deletedGift, $this->getUser());
            $this->get('event_dispatcher')->dispatch(GiftDeletedEvent::NAME, $event);

            $this->addFlash('notice', sprintf('Gift "%s" deleted', $deletedGift->getName()));
        }

        return $this->redirectToRoute('category_show', ['id' => $gift->getCategory()->getId()]);
    }

    /**
     * @param Request $request
     * @param Gift    $gift
     * @Route("/{id}/mark-received", name="gift_mark_received", requirements={"id": "\d+"})
     * @Method({"POST"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function markReceivedAction(Request $request, Gift $gift)
    {
        // Access control
        $this->checkAccess(
            $gift->isSurprise() ? ['OWNER', 'EDIT', 'SURPRISE_ADD'] : ['OWNER', 'EDIT'],
            $gift->getCategory()->getList()
        );

        $form = $this->createSimpleActionForm($gift, 'mark_received');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gift
                ->setReceived(true)
                ->setReceivedDate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($gift);
            $em->flush();

            $this->addFlash('notice', sprintf('Gift "%s" marked as received', $gift->getName()));
        }

        return $this->redirectToRoute('category_show', ['id' => $gift->getCategory()->getId()]);
    }

    /**
     * @param Request $request
     * @param Gift    $gift
     * @Route("/{id}/mark-bought", name="gift_mark_bought", requirements={"id": "\d+"})
     * @Method({"POST"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function markBoughtAction(Request $request, Gift $gift)
    {
        // Access control
        $this->checkAccess('SURPRISE_ADD', $gift->getCategory()->getList());

        $form = $this->createPurchaseForm($gift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $purchaseComment = $data['purchaseComment'];
            $gift
                ->setBought(true)
                ->setBuyer($this->getUser())
                ->setPurchaseDate(new \DateTime());
            if (!empty($purchaseComment)) {
                $gift->setPurchaseComment($purchaseComment);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($gift);
            $em->flush();

            // Dispatch the purchase event
            $event = new GiftPurchasedEvent($gift, $this->getUser(), $purchaseComment);
            $this->get('event_dispatcher')->dispatch(GiftPurchasedEvent::NAME, $event);

            $this->addFlash('notice', sprintf('Gift "%s" marked as bought', $gift->getName()));
        }

        return $this->redirectToRoute('gift_show', ['id' => $gift->getId()]);
    }

    /**
     * Creates a form for simple actions
     *
     * @param Gift   $gift
     * @param string $action Chosen action
     *
     * @return \Symfony\Component\Form\Form|\Symfony\Component\HttpFoundation\RedirectResponse Delete form or redirect
     */
    private function createSimpleActionForm(Gift $gift, $action = 'delete')
    {
        switch ($action) {
            case 'delete':
                $method = 'DELETE';
                $url = $this->generateUrl('gift_delete', ['id' => $gift->getId()]);
                break;
            case 'mark_received':
                $method = 'POST';
                $url = $this->generateUrl('gift_mark_received', ['id' => $gift->getId()]);
                break;
            default:
                return $this->redirectToRoute('category_show', ['id' => $gift->getCategory()->getId()]);
        }

        return $this->createFormBuilder()
            ->setAction($url)
            ->setMethod($method)
            ->getForm();
    }

    /**
     * Creates a form for the purchase action
     * @param Gift $gift
     * @return \Symfony\Component\Form\Form
     */
    private function createPurchaseForm(Gift $gift)
    {
        return $this->createFormBuilder()
            ->add('purchaseComment', TextareaType::class, ['constraints' => [ new Length(['max' => 1000])]])
            ->setAction($this->generateUrl('gift_mark_bought', ['id' => $gift->getId()]))
            ->setMethod('POST')
            ->getForm();
    }
}
