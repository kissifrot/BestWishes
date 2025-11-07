<?php

namespace BestWishes\EventSubscriber;

use BestWishes\Entity\User;
use BestWishes\Event\GiftCreatedEvent;
use BestWishes\Event\GiftDeletedEvent;
use BestWishes\Event\GiftEditedEvent;
use BestWishes\Event\GiftPurchasedEvent;
use BestWishes\Event\GiftReceivedEvent;
use BestWishes\Manager\DoctrineCacheManager;
use BestWishes\Manager\UserManager;
use BestWishes\Message\CreationAlertMessage;
use BestWishes\Message\DeletionAlertMessage;
use BestWishes\Message\EditionAlertMessage;
use BestWishes\Message\PurchaseAlertMessage;
use BestWishes\Security\Core\BestWishesSecurityContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GiftSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserManager               $userManager,
        private readonly BestWishesSecurityContext $securityContext,
        private readonly MessageBusInterface       $messageBus,
        private readonly DoctrineCacheManager      $cacheManager,
        private readonly UrlGeneratorInterface     $router,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GiftCreatedEvent::NAME   => 'onGiftCreation',
            GiftDeletedEvent::NAME   => 'onGiftDeletion',
            GiftPurchasedEvent::NAME => 'onGiftPurchase',
            GiftEditedEvent::NAME    => 'onGiftEdition',
            GiftReceivedEvent::NAME  => 'onGiftReceived',
        ];
    }

    public function onGiftCreation(GiftCreatedEvent $event): void
    {
        $list  = $event->getGift()->getList();
        $users = $this->userManager->findUsers();

        $mailedUsers = [];
        foreach ($users as $anUser) {
            if ($anUser === $event->getCreator()) {
                // Skip current user
                continue;
            }
            if ($this->securityContext->isGranted('OWNER', $list, $anUser)) {
                // Skip owner
                continue;
            }
            if ($this->securityContext->isGranted('ALERT_ADD', $list, $anUser)) {
                $mailedUsers[] = $anUser;
            }
        }

        $this->cacheManager->clearGiftListCache($list);
        /** @var User $creator */
        $creator = $event->getCreator();
        $homeUrl = $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);

        if (!empty($mailedUsers)) {
            foreach ($mailedUsers as $mailedUser) {
                $this->messageBus->dispatch(new CreationAlertMessage($mailedUser->getId(), $event->getGift()->getId(), $creator->getId(), $homeUrl));
            }
        }
    }

    public function onGiftDeletion(GiftDeletedEvent $event): void
    {
        $list  = $event->getGift()->getList();
        $users = $this->userManager->findUsers();

        $mailedUsers = [];
        foreach ($users as $anUser) {
            if ($anUser === $event->getDeleter()) {
                // Skip current user
                continue;
            }
            if ($this->securityContext->isGranted('OWNER', $list, $anUser)) {
                // Skip owner
                continue;
            }
            if ($this->securityContext->isGranted('ALERT_DELETE', $list, $anUser)) {
                $mailedUsers[] = $anUser;
            }
        }

        $this->cacheManager->clearGiftListCache($list);
        /** @var User $deleter */
        $deleter = $event->getDeleter();
        $homeUrl = $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);

        if (!empty($mailedUsers)) {
            foreach ($mailedUsers as $mailedUser) {
                $this->messageBus->dispatch(new DeletionAlertMessage($mailedUser->getId(), $event->getGift()->getId(), $deleter->getId(), $homeUrl));
            }
        }
    }

    public function onGiftPurchase(GiftPurchasedEvent $event): void
    {
        $list  = $event->getGift()->getList();
        $users = $this->userManager->findUsers();

        $mailedUsers = [];
        foreach ($users as $anUser) {
            if ($anUser === $event->getBuyer()) {
                // Skip current user
                continue;
            }
            if ($this->securityContext->isGranted('OWNER', $list, $anUser)) {
                // Skip owner
                continue;
            }
            if ($this->securityContext->isGranted('ALERT_PURCHASE', $list, $anUser)) {
                $mailedUsers[] = $anUser;
            }
        }
        /** @var User $buyer */
        $buyer = $event->getBuyer();
        $homeUrl = $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);

        if (!empty($mailedUsers)) {
            foreach ($mailedUsers as $mailedUser) {
                $this->messageBus->dispatch(new PurchaseAlertMessage($mailedUser->getId(), $event->getGift()->getId(), $buyer->getId(), $homeUrl));
            }
        }
    }

    public function onGiftReceived(GiftReceivedEvent $event): void
    {
        $this->cacheManager->clearGiftListCache($event->getGift()->getList());
    }

    public function onGiftEdition(GiftEditedEvent $event): void
    {
        $list  = $event->getEditedGift()->getList();
        $users = $this->userManager->findUsers();
        $homeUrl = $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $mailedUsers = [];
        foreach ($users as $anUser) {
            if ($anUser === $event->getEditor()) {
                // Skip current user
                continue;
            }
            if ($this->securityContext->isGranted('OWNER', $list, $anUser)) {
                // Skip owner
                continue;
            }
            if ($this->securityContext->isGranted('ALERT_EDIT', $list, $anUser)) {
                $mailedUsers[] = $anUser;
            }
        }
        /** @var User $editor */
        $editor = $event->getEditor();

        if (!empty($mailedUsers)) {
            foreach ($mailedUsers as $mailedUser) {
                $this->messageBus->dispatch(new EditionAlertMessage($mailedUser->getId(), $event->getEditedGift()->getId(), $editor->getId(), $homeUrl));
            }
        }
    }
}
