<?php

namespace BestWishes\Message;

class DeletionAlertMessage
{
    public function __construct(
        private readonly int $mailedUserId,
        private readonly int $giftId,
        private readonly int $deleterId,
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

    public function getDeleterId(): int
    {
        return $this->deleterId;
    }
}
