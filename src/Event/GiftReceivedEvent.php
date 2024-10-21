<?php

namespace BestWishes\Event;

use BestWishes\Entity\Gift;
use Symfony\Contracts\EventDispatcher\Event;

class GiftReceivedEvent extends Event
{
    final public const NAME = 'gift.received';

    public function __construct(protected Gift $gift)
    {
    }

    public function getGift(): Gift
    {
        return $this->gift;
    }
}
