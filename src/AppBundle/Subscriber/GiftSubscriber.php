<?php

namespace AppBundle\Subscriber;

use AppBundle\Event\GiftCreatedEvent;
use AppBundle\Event\GiftDeletedEvent;
use AppBundle\Event\GiftEditedEvent;
use AppBundle\Event\GiftPurchasedEvent;
use AppBundle\Mailer\Mailer;
use AppBundle\Security\Core\BestWishesSecurityContext;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GiftSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var BestWishesSecurityContext
     */
    private $securityContext;
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * GiftSubscriber constructor.
     * @param UserManager               $userManager
     * @param BestWishesSecurityContext $securityContext
     * @param Mailer                    $mailer
     */
    public function __construct(UserManager $userManager, BestWishesSecurityContext $securityContext, Mailer $mailer)
    {
        $this->userManager     = $userManager;
        $this->securityContext = $securityContext;
        $this->mailer          = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            GiftCreatedEvent::NAME   => 'onGiftCreation',
            GiftDeletedEvent::NAME   => 'onGiftDeletion',
            GiftPurchasedEvent::NAME => 'onGiftPurchase',
            GiftEditedEvent::NAME    => 'onGiftEdition'
        ];
    }

    public function onGiftCreation(GiftCreatedEvent $event)
    {
        $list  = $event->getGift()->getCategory()->getList();
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

        if (!empty($mailedUsers)) {
            foreach($mailedUsers as $mailedUser) {
                $this->mailer->sendCreationAlertMessage($mailedUser, $event->getGift());
            }
        }
    }

    public function onGiftDeletion(GiftDeletedEvent $event)
    {
        $list  = $event->getGift()->getCategory()->getList();
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
    }

    public function onGiftPurchase(GiftPurchasedEvent $event)
    {
        $list  = $event->getGift()->getCategory()->getList();
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
            foreach($mailedUsers as $mailedUser) {
                $this->mailer->sendPurchaseAlertMessage($mailedUser, $event->getGift());
            }
        }
    }

    public function onGiftEdition(GiftEditedEvent $event)
    {
        $list  = $event->getEditedGift()->getCategory()->getList();
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
            foreach($mailedUsers as $mailedUser) {
                $this->mailer->sendEditionAlertMessage($mailedUser, $event->getEditedGift());
            }
        }
    }
}
