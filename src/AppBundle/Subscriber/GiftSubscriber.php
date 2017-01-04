<?php

namespace AppBundle\Subscriber;


use AppBundle\Event\GiftCreatedEvent;
use AppBundle\Event\GiftDeletedEvent;
use AppBundle\Event\GiftEditedEvent;
use AppBundle\Event\GiftPurchasedEvent;
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
     * GiftSubscriber constructor.
     * @param UserManager               $userManager
     * @param BestWishesSecurityContext $securityContext
     */
    public function __construct(UserManager $userManager, BestWishesSecurityContext $securityContext)
    {
        $this->userManager = $userManager;
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            GiftCreatedEvent::NAME => 'onGiftCreation',
            GiftDeletedEvent::NAME => 'onGiftDeletion',
            GiftPurchasedEvent::NAME => 'onGiftPurchase',
            GiftEditedEvent::NAME => 'onGiftEdition'
        ];
    }

    public function onGiftCreation(GiftCreatedEvent $event)
    {
        $list = $event->getGift()->getCategory()->getList();
        $users = $this->userManager->findUsers();

        foreach($users as $anUser) {
            if($anUser === $event->getCreator()) {
                // Skip current user
                continue;
            }
            if($this->securityContext->isGranted('OWNER', $list, $anUser)) {
                // Skip owner
                continue;
            }
            if($this->securityContext->isGranted('ALERT_ADD', $list, $anUser)) {
                // TODO: Add user to the alerted list
            }
        }
    }

    public function onGiftDeletion(GiftDeletedEvent $event)
    {
        $list = $event->getGift()->getCategory()->getList();
        $users = $this->userManager->findUsers();
        foreach($users as $anUser) {
            if($anUser === $event->getDeleter()) {
                // Skip current user
                continue;
            }
            if($this->securityContext->isGranted('OWNER', $list, $anUser)) {
                // Skip owner
                continue;
            }
            if($this->securityContext->isGranted('ALERT_DELETE', $list, $anUser)) {
                // TODO: Add user to the alerted list
            }
        }
    }

    public function onGiftPurchase(GiftPurchasedEvent $event)
    {
        $list = $event->getGift()->getCategory()->getList();
        $users = $this->userManager->findUsers();
        foreach($users as $anUser) {
            if($anUser === $event->getBuyer()) {
                // Skip current user
                continue;
            }
            if($this->securityContext->isGranted('OWNER', $list, $anUser)) {
                // Skip owner
                continue;
            }
            if($this->securityContext->isGranted('ALERT_PURCHASE', $list, $anUser)) {
                // TODO: Add user to the alerted list
            }
        }
    }

    public function onGiftEdition(GiftEditedEvent $event)
    {
        $list = $event->getEditedGift()->getCategory()->getList();
        $users = $this->userManager->findUsers();
        foreach($users as $anUser) {
            if($anUser === $event->getEditor()) {
                // Skip current user
                continue;
            }
            if($this->securityContext->isGranted('OWNER', $list, $anUser)) {
                // Skip owner
                continue;
            }
            if($this->securityContext->isGranted('ALERT_EDIT', $list, $anUser)) {
                // TODO: Add user to the alerted list
            }
        }
    }
}
