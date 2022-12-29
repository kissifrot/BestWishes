<?php

namespace BestWishes\Event;

use BestWishes\Entity\Gift;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class GiftPurchasedEvent extends Event
{
    final public const NAME = 'gift.purchased';

    public function __construct(protected Gift $gift, private readonly UserInterface $buyer, private ?string $purchaseComment = null)
    {
    }

    public function getGift(): Gift
    {
        return $this->gift;
    }

    public function getBuyer(): UserInterface
    {
        return $this->buyer;
    }

    public function getPurchaseComment(): ?string
    {
        return $this->purchaseComment;
    }
}
