<?php

namespace BestWishes\Message;

class EditionAlertMessage
{
    public function __construct(
        private readonly int $mailedUserId,
        private readonly int $giftId,
        private readonly int $editorId,
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

    public function getEditorId(): int
    {
        return $this->editorId;
    }

    public function getHomeUrl(): string
    {
        return $this->homeUrl;
    }
}
