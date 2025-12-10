<?php

namespace BestWishes\EventSubscriber;

use BestWishes\Entity\ListEvent;
use BestWishes\Event\GiftListCreatedEvent;
use BestWishes\Repository\ListEventRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GiftListSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly ListEventRepository $listEventRepository)
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GiftListCreatedEvent::NAME => 'onGiftListCreation',
        ];
    }

    /**
     * Ensure there's at least the birthdate event
     */
    public function onGiftListCreation(GiftListCreatedEvent $event): void
    {
        $birthdateEvent = $this->listEventRepository->findBirthdate();
        if (null !== $birthdateEvent) {
            return;
        }
        $birthdateEvent = new ListEvent(true, ListEvent::BIRTHDAY_TYPE);
        $this->listEventRepository->save($birthdateEvent, flush: true);
    }
}
