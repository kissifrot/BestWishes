<?php

namespace BestWishes\Event;

use BestWishes\Entity\Gift;
use Symfony\Contracts\EventDispatcher\Event;

class GiftReceivedEvent extends Event
{
    public const NAME = 'gift.received';

    /**
     * @var Gift
     */
    protected $gift;

    public function __construct(Gift $gift)
    {
        $this->gift = $gift;
    }

    /**
     * @return Gift
     */
    public function getGift(): Gift
    {
        return $this->gift;
    }
}
