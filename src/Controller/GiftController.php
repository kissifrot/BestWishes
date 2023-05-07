<?php

namespace BestWishes\Controller;

use BestWishes\Entity\Category;
use BestWishes\Entity\Gift;
use BestWishes\Entity\User;
use BestWishes\Event\GiftCreatedEvent;
use BestWishes\Event\GiftDeletedEvent;
use BestWishes\Event\GiftEditedEvent;
use BestWishes\Event\GiftPurchasedEvent;
use BestWishes\Event\GiftReceivedEvent;
use BestWishes\Form\Type\GiftType;
use BestWishes\Manager\SecurityManager;
use BestWishes\Repository\GiftRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: 'gift')]
class GiftController extends AbstractController
{
    public function __construct(
        private readonly GiftRepository           $giftRepository,
        private readonly SecurityManager          $securityManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly TranslatorInterface      $translator
    ) {
    }

    #[Route(path: '/{id}', name: 'gift_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request): Response
    {
        $id = $request->attributes->getInt('id');
        $gift = $this->isGranted('IS_AUTHENTICATED_REMEMBERED')
            ? $this->giftRepository->findFullById($id)
            : $this->giftRepository->findFullSurpriseExcludedById($id)
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

    #[Route(path: '/create/{catId}', name: 'gift_create', requirements: ['catId' => '\d+'], methods: ['GET', 'POST'])]
    public function create(Request $request, #[MapEntity(expr: 'repository.find(catId)')] Category $category): Response
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
            $this->giftRepository->save($gift, flush: true);

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

    #[Route(path: '{id}/edit', name: 'gift_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
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
            $this->giftRepository->save($gift, flush: true);

            // Dispatch the edition event
            $this->eventDispatcher->dispatch(new GiftEditedEvent($originGift, $gift, $this->getUser()), GiftEditedEvent::NAME);

            $this->addFlash('notice', $this->translator->trans('gift.message.updated', ['%giftName%' => $gift->getName()]));

            return $this->redirectToRoute('gift_show', ['id' => $gift->getId()]);
        }

        return $this->render('gift/edit.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '{id}/delete', name: 'gift_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
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
            $this->giftRepository->remove($gift, flush: true);

            // Dispatch the deletion event
            $this->eventDispatcher->dispatch(new GiftDeletedEvent($deletedGift, $this->getUser()), GiftDeletedEvent::NAME);

            $this->addFlash('notice', $this->translator->trans('gift.message.deleted', ['%giftName%' => $deletedGift->getName()]));
        }

        return $this->redirectToRoute('category_show', ['id' => $gift->getCategoryId()]);
    }

    #[Route(path: '{id}/mark-received', name: 'gift_mark_received', requirements: ['id' => '\d+'], methods: ['POST'])]
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
            $this->giftRepository->save($gift, flush: true);

            // Dispatch the received event
            $this->eventDispatcher->dispatch(new GiftReceivedEvent($receivedGift), GiftReceivedEvent::NAME);

            $this->addFlash('notice', $this->translator->trans('gift.message.marked_received', ['%giftName%' => $gift->getName()]));
        }

        return $this->redirectToRoute('category_show', ['id' => $gift->getCategoryId()]);
    }

    #[Route(path: '{id}/mark-bought', name: 'gift_mark_bought', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function markBought(Request $request, Gift $gift): Response
    {
        $this->securityManager->checkAccess('SURPRISE_ADD', $gift->getList());

        $form = $this->createPurchaseForm($gift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $purchaseComment = $data['purchaseComment'];

            /** @var User $user */
            $user = $this->getUser();
            $gift->markPurchasedBy($user, $data['purchaseComment']);
            $this->giftRepository->save($gift, flush: true);

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
     */
    private function createSimpleActionForm(Gift $gift, string $action = 'delete'): FormInterface
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
                throw new \UnexpectedValueException(sprintf('Unable to create for for action "%s"', $action));
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
