<?php

namespace AppBundle\Event;

use AppBundle\Entity\Gift;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class GiftPurchasedEvent extends Event
{
    const NAME = 'gift.purchased';

    /**
     * @var Gift
     */
    protected $gift;

    /**
     * @var UserInterface
     */
    private $buyer;

    public function __construct(Gift $gift, UserInterface $buyer)
    {
        $this->gift = $gift;
        $this->buyer = $buyer;
    }

    /**
     * @return Gift
     */
    public function getGift()
    {
        return $this->gift;
    }

    /**
     * @return UserInterface
     */
    public function getBuyer()
    {
        return $this->buyer;
    }
}
