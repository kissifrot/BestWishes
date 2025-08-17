<?php

namespace BestWishes\MessageHandler;

use BestWishes\Mailer\Mailer;
use BestWishes\Message\DeletionAlertMessage;
use BestWishes\Repository\GiftRepository;
use BestWishes\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeletionAlertMessageHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly GiftRepository $giftRepository,
        private readonly Mailer         $mailer,
    ) {
    }

    public function __invoke(DeletionAlertMessage $message): void
    {
        $mailedUser = $this->userRepository->find($message->getMailedUserId());
        $gift = $this->giftRepository->find($message->getGiftId());
        $deleter = $this->userRepository->find($message->getDeleterId());
        if (null === $mailedUser || null === $gift || null === $deleter) {
            return;
        }
        $this->mailer->sendDeletionAlertMessage($mailedUser, $gift, $deleter);
    }
}
