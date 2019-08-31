<?php

namespace BestWishes\EventSubscriber;

use BestWishes\Entity\ListEvent;
use BestWishes\Event\GiftListCreatedEvent;
use BestWishes\Manager\ListEventManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GiftListSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GiftListCreatedEvent::NAME => 'onGiftListCreation'
        ];
    }

    /**
     * Ensure there's at least the birth date event
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onGiftListCreation(GiftListCreatedEvent $event): void
    {
        $birthdateEvent = $this->entityManager->getRepository(ListEvent::class)->findBirthdate();
        if (null !== $birthdateEvent) {
            return;
        }
        $birthdateEvent = new ListEvent(true, ListEvent::BIRTHDAY_TYPE);
        $this->entityManager->persist($birthdateEvent);
        $this->entityManager->flush();
    }
}
