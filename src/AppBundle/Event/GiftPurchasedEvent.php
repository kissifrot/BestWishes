<?php

namespace AppBundle\Event;

use AppBundle\Entity\Gift;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class GiftPurchasedEvent extends Event
{
    public const NAME = 'gift.purchased';

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
    public function __construct(Gift $gift, UserInterface $buyer, string $purchaseComment = '')
    {
        $this->gift = $gift;
        $this->buyer = $buyer;
        $this->purchaseComment = $purchaseComment;
    }

    /**
     * @return Gift
     */
    public function getGift(): Gift
    {
        return $this->gift;
    }

    /**
     * @return UserInterface
     */
    public function getBuyer(): UserInterface
    {
        return $this->buyer;
    }

    /**
     * @return string
     */
    public function getPurchaseComment(): string
    {
        return $this->purchaseComment;
    }
}
