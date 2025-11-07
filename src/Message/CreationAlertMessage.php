<?php

namespace BestWishes\Message;

class CreationAlertMessage
{
    public function __construct(
        private readonly int $mailedUserId,
        private readonly int $giftId,
        private readonly int $creatorId,
        private readonly string $homeUrl,
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

    public function getCreatorId(): int
    {
        return $this->creatorId;
    }

    public function getHomeUrl(): string
    {
        return $this->homeUrl;
    }
}
