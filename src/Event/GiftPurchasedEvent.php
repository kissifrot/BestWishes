<?php

namespace BestWishes\Event;

use BestWishes\Entity\Gift;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

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
    public function __construct(Gift $gift, UserInterface $buyer, ?string $purchaseComment = '')
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
    public function getPurchaseComment(): ?string
    {
        return $this->purchaseComment;
    }
}
