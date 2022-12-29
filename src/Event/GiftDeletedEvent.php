<?php

namespace BestWishes\Event;

use BestWishes\Entity\Gift;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class GiftDeletedEvent extends Event
{
    final public const NAME = 'gift.deleted';

    public function __construct(protected Gift $gift, private readonly UserInterface $deleter)
    {
    }

    public function getGift(): Gift
    {
        return $this->gift;
    }

    public function getDeleter(): UserInterface
    {
        return $this->deleter;
    }
}
