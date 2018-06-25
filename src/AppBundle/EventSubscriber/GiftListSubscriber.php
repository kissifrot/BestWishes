<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\ListEvent;
use AppBundle\Event\GiftListCreatedEvent;
use AppBundle\Manager\ListEventManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GiftListSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * GiftSubscriber constructor.
     * @param EntityManagerInterface $entityManager
     */
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
     * Ensure there's at least the birthdate event
     *
     * @param GiftListCreatedEvent $event
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onGiftListCreation(GiftListCreatedEvent $event): void
    {
        $birthdateEvent = $this->entityManager->getRepository('AppBundle:ListEvent')->findBirthdate();
        if (null !== $birthdateEvent) {
            return;
        }
        $birthdateEvent = new ListEvent();
        $birthdateEvent
            ->setPermanent(true)
            ->setType(ListEventManager::BIRTHDAY_TYPE);
        $this->entityManager->persist($birthdateEvent);
        $this->entityManager->flush();
    }
}
