<?php

namespace BestWishes\Controller;

use BestWishes\Entity\Category;
use BestWishes\Entity\Gift;
use BestWishes\Event\GiftCreatedEvent;
use BestWishes\Event\GiftDeletedEvent;
use BestWishes\Event\GiftEditedEvent;
use BestWishes\Event\GiftPurchasedEvent;
use BestWishes\Event\GiftReceivedEvent;
use BestWishes\Form\Type\GiftType;
use BestWishes\Manager\SecurityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("gift")
 */
class GiftController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SecurityManager $securityManager;
    private EventDispatcherInterface $eventDispatcher;
    private TranslatorInterface $translator;

    public function __construct(EntityManagerInterface $entityManager, SecurityManager $securityManager, EventDispatcherInterface $eventDispatcher, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->securityManager = $securityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->translator = $translator;
    }

    /**
     * @Route("/{id}", name="gift_show", requirements={"id": "\d+"}, methods={"GET"})
     *
     * @return Response|NotFoundHttpException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request)
    {
        $id = $request->attributes->getInt('id');
        $gift = $this->isGranted('IS_AUTHENTICATED_REMEMBERED')
            ? $this->entityManager->getRepository(Gift::class)->findFullById($id)
            : $this->entityManager->getRepository(Gift::class)->findFullSurpriseExcludedById($id)
        ;
        if (!$gift) {
            throw $this->createNotFoundException();
        }
        $deleteForm = $this->createSimpleActionForm($gift, 'delete')->createView();
        $markReceivedForm = $this->createSimpleActionForm($gift, 'mark_received')->createView();
        $markBoughtForm = $this->createPurchaseForm($gift)->createView();

        return $this->render(
            'gift/show.html.twig',
            compact('gift', 'deleteForm', 'markReceivedForm', 'markBoughtForm')
        );
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/create/{catId}", name="gift_create", requirements={"catId": "\d+"}, methods={"GET", "POST"})
     * @ParamConverter("category", options={"id" = "catId"})
     */
    public function create(Request $request, Category $category): Response
    {
        $isSurprise = $request->query->getBoolean('surprise');

        // Access control
        $this->securityManager->checkAccess(
            $isSurprise ? ['EDIT', 'SURPRISE_ADD'] : ['OWNER', 'EDIT'],
            $category->getList()
        );

        $gift = new Gift($isSurprise, $category);
        $form = $this->createForm(GiftType::class, $gift);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($gift);
            $this->entityManager->flush();

            // Dispatch the creation event
            $this->eventDispatcher->dispatch(new GiftCreatedEvent($gift, $this->getUser()), GiftCreatedEvent::NAME);

            $this->addFlash('notice', $this->translator->trans('gift.message.created', ['%giftName%' => $gift->getName()]));

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return $this->render(
            'gift/create.html.twig',
            ['form' => $form->createView(), 'category' => $category, 'isSurprise' => $isSurprise]
        );
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("{id}/edit", name="gift_edit", requirements={"id": "\d+"}, methods={"GET", "POST"})
     */
    public function edit(Request $request, Gift $gift): Response
    {
        $this->securityManager->checkAccess(
            $gift->isSurprise() ? ['OWNER', 'EDIT', 'SURPRISE_ADD'] : ['OWNER', 'EDIT'],
            $gift->getList()
        );

        $originGift = clone $gift;
        $form = $this->createForm(GiftType::class, $gift);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($gift);
            $this->entityManager->flush();

            // Dispatch the edition event
            $this->eventDispatcher->dispatch(new GiftEditedEvent($originGift, $gift, $this->getUser()), GiftEditedEvent::NAME);

            $this->addFlash('notice', $this->translator->trans('gift.message.updated', ['%giftName%' => $gift->getName()]));

            return $this->redirectToRoute('gift_show', ['id' => $gift->getId()]);
        }

        return $this->render('gift/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/delete", name="gift_delete", requirements={"id": "\d+"}, methods={"POST"})
     */
    public function delete(Request $request, Gift $gift): Response
    {
        $this->securityManager->checkAccess(
            $gift->isSurprise() ? ['OWNER', 'EDIT', 'SURPRISE_ADD'] : ['OWNER', 'EDIT'],
            $gift->getList()
        );

        $form = $this->createSimpleActionForm($gift, 'delete');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $deletedGift = clone $gift;
            $this->entityManager->remove($gift);
            $this->entityManager->flush();

            // Dispatch the deletion event
            $this->eventDispatcher->dispatch(new GiftDeletedEvent($deletedGift, $this->getUser()), GiftDeletedEvent::NAME);

            $this->addFlash('notice', $this->translator->trans('gift.message.deleted', ['%giftName%' => $deletedGift->getName()]));
        }

        return $this->redirectToRoute('category_show', ['id' => $gift->getCategoryId()]);
    }

    /**
     * @Route("/{id}/mark-received", name="gift_mark_received", requirements={"id": "\d+"}, methods={"POST"})
     */
    public function markReceived(Request $request, Gift $gift): Response
    {
        $this->securityManager->checkAccess(
            $gift->isSurprise() ? ['OWNER', 'EDIT', 'SURPRISE_ADD'] : ['OWNER', 'EDIT'],
            $gift->getList()
        );

        $form = $this->createSimpleActionForm($gift, 'mark_received');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gift->markReceived();
            $receivedGift = clone $gift;
            $this->entityManager->persist($gift);
            $this->entityManager->flush();

            // Dispatch the received event
            $this->eventDispatcher->dispatch(new GiftReceivedEvent($receivedGift), GiftReceivedEvent::NAME);

            $this->addFlash('notice', $this->translator->trans('gift.message.marked_received', ['%giftName%' => $gift->getName()]));
        }

        return $this->redirectToRoute('category_show', ['id' => $gift->getCategoryId()]);
    }

    /**
     * @Route("/{id}/mark-bought", name="gift_mark_bought", requirements={"id": "\d+"}, methods={"POST"})
     *
     */
    public function markBought(Request $request, Gift $gift): Response
    {
        $this->securityManager->checkAccess('SURPRISE_ADD', $gift->getList());

        $form = $this->createPurchaseForm($gift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $purchaseComment = $data['purchaseComment'];

            $gift->markPurchasedBy($this->getUser(), $data['purchaseComment']);
            $this->entityManager->persist($gift);
            $this->entityManager->flush();

            // Dispatch the purchase event
            $this->eventDispatcher->dispatch(new GiftPurchasedEvent($gift, $this->getUser(), $purchaseComment), GiftPurchasedEvent::NAME);

            $this->addFlash('notice', $this->translator->trans('gift.message.marked_bought', ['%giftName%' => $gift->getName()]));
        }

        return $this->redirectToRoute('gift_show', ['id' => $gift->getId()]);
    }

    /**
     * Creates a form for simple actions
     *
     * @param string $action Chosen action
     *
     * @return FormInterface|RedirectResponse Delete form or redirect
     */
    private function createSimpleActionForm(Gift $gift, $action = 'delete')
    {
        switch ($action) {
            case 'delete':
                $method = 'POST';
                $url = $this->generateUrl('gift_delete', ['id' => $gift->getId()]);
                break;
            case 'mark_received':
                $method = 'POST';
                $url = $this->generateUrl('gift_mark_received', ['id' => $gift->getId()]);
                break;
            default:
                return $this->redirectToRoute('category_show', ['id' => $gift->getCategoryId()]);
        }

        return $this->createFormBuilder()
            ->setAction($url)
            ->setMethod($method)
            ->getForm();
    }

    /**
     * Creates a form for the purchase action
     */
    private function createPurchaseForm(Gift $gift): FormInterface
    {
        return $this->createFormBuilder()
            ->add('purchaseComment', TextareaType::class, ['label_format' => 'form.gift.purchaseComment', 'constraints' => [ new Length(['max' => 1000])]])
            ->setAction($this->generateUrl('gift_mark_bought', ['id' => $gift->getId()]))
            ->setMethod('POST')
            ->getForm();
    }
}
