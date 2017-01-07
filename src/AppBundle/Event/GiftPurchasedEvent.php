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

    /**
     * @var string
     */
    private $purchaseComment;

    /**
     * @param Gift          $gift
     * @param UserInterface $buyer
     * @param string        $purchaseComment
     */
    public function __construct(Gift $gift, UserInterface $buyer, $purchaseComment = '')
    {
        $this->gift = $gift;
        $this->buyer = $buyer;
        $this->purchaseComment = $purchaseComment;
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

    /**
     * @return string
     */
    public function getPurchaseComment()
    {
        return $this->purchaseComment;
    }
}
