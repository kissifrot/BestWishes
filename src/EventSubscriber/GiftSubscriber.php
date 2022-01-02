<?php

namespace BestWishes\EventSubscriber;

use BestWishes\Event\GiftCreatedEvent;
use BestWishes\Event\GiftDeletedEvent;
use BestWishes\Event\GiftEditedEvent;
use BestWishes\Event\GiftPurchasedEvent;
use BestWishes\Event\GiftReceivedEvent;
use BestWishes\Mailer\Mailer;
use BestWishes\Manager\DoctrineCacheManager;
use BestWishes\Manager\UserManager;
use BestWishes\Security\Core\BestWishesSecurityContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GiftSubscriber implements EventSubscriberInterface
{
    private UserManager $userManager;
    private BestWishesSecurityContext $securityContext;
    private Mailer $mailer;
    private DoctrineCacheManager $cacheManager;

    public function __construct(UserManager $userManager, BestWishesSecurityContext $securityContext, Mailer $mailer, DoctrineCacheManager $cacheManager)
    {
        $this->userManager     = $userManager;
        $this->securityContext = $securityContext;
        $this->mailer          = $mailer;
        $this->cacheManager = $cacheManager;
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

        if (!empty($mailedUsers)) {
            foreach ($mailedUsers as $mailedUser) {
                $this->mailer->sendCreationAlertMessage($mailedUser, $event->getGift(), $event->getCreator());
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

        if (!empty($mailedUsers)) {
            foreach ($mailedUsers as $mailedUser) {
                $this->mailer->sendDeletionAlertMessage($mailedUser, $event->getGift(), $event->getDeleter());
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

        if (!empty($mailedUsers)) {
            foreach ($mailedUsers as $mailedUser) {
                $this->mailer->sendPurchaseAlertMessage($mailedUser, $event->getGift(), $event->getBuyer());
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

        if (!empty($mailedUsers)) {
            foreach ($mailedUsers as $mailedUser) {
                $this->mailer->sendEditionAlertMessage($mailedUser, $event->getEditedGift(), $event->getEditor());
            }
        }
    }
}
