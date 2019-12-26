<?php


namespace BestWishes\Event;

use Symfony\Contracts\EventDispatcher\Event;

class GiftListCreatedEvent extends Event
{
    public const NAME = 'giftlist.created';
}
