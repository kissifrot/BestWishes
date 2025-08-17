<?php

namespace BestWishes\MessageHandler;

use BestWishes\Mailer\Mailer;
use BestWishes\Message\EditionAlertMessage;
use BestWishes\Repository\GiftRepository;
use BestWishes\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EditionAlertMessageHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly GiftRepository $giftRepository,
        private readonly Mailer         $mailer,
    ) {
    }

    public function __invoke(EditionAlertMessage $message): void
    {
        $mailedUser = $this->userRepository->find($message->getMailedUserId());
        $gift = $this->giftRepository->find($message->getGiftId());
        $editor = $this->userRepository->find($message->getEditorId());
        if (null === $mailedUser || null === $gift || null === $editor) {
            return;
        }
        $this->mailer->sendEditionAlertMessage($mailedUser, $gift, $editor);
    }
}
