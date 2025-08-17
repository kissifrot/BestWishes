<?php

namespace BestWishes\Message;

class PurchaseAlertMessage
{
    public function __construct(
        private readonly int $mailedUserId,
        private readonly int $giftId,
        private readonly int $buyerId,
    ) {
    }

    public function getMailedUserId(): int
    {
        return $this->mailedUserId;
    }

    public function getGiftId(): int
    {
        return $this->giftId;
    }

    public function getBuyerId(): int
    {
        return $this->buyerId;
    }
}
