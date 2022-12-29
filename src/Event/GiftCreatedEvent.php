<?php

namespace BestWishes\Event;

use BestWishes\Entity\Gift;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class GiftCreatedEvent extends Event
{
    final public const NAME = 'gift.created';

    public function __construct(protected Gift $gift, private readonly UserInterface $creator)
    {
    }

    /**
     */
    public function getGift(): Gift
    {
        return $this->gift;
    }

    /**
     */
    public function getCreator(): UserInterface
    {
        return $this->creator;
    }
}
