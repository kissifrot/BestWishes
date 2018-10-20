<?php

namespace BestWishes\EventSubscriber;

use BestWishes\Event\CategoryCreatedEvent;
use BestWishes\Event\CategoryDeletedEvent;
use BestWishes\Event\CategoryEditedEvent;
use BestWishes\Manager\DoctrineCacheManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CategorySubscriber implements EventSubscriberInterface
{
    private $cacheManager;

    public function __construct(DoctrineCacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CategoryCreatedEvent::NAME   => 'onCategoryCreation',
            CategoryEditedEvent::NAME   => 'onCategoryEdition',
            CategoryDeletedEvent::NAME => 'onCategoryDeletion'
        ];
    }

    public function onCategoryCreation(CategoryCreatedEvent $event): void
    {
        $this->cacheManager->clearGiftListCache($event->getCategory()->getList());
    }

    public function onCategoryDeletion(CategoryDeletedEvent $event): void
    {
        $this->cacheManager->clearGiftListCache($event->getCategory()->getList());
    }

    public function onCategoryEdition(CategoryEditedEvent $event): void
    {
        $this->cacheManager->clearGiftListCache($event->getCategory()->getList());
    }
}
