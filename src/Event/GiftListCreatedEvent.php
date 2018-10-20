<?php


namespace BestWishes\Event;


use Symfony\Component\EventDispatcher\Event;

class GiftListCreatedEvent extends Event
{
    public const NAME = 'giftlist.created';
}
