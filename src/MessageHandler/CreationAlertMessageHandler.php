<?php

namespace BestWishes\MessageHandler;

use BestWishes\Mailer\Mailer;
use BestWishes\Message\CreationAlertMessage;
use BestWishes\Repository\GiftRepository;
use BestWishes\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreationAlertMessageHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly GiftRepository $giftRepository,
        private readonly Mailer         $mailer,
    ) {
    }

    public function __invoke(CreationAlertMessage $message): void
    {
        $mailedUser = $this->userRepository->find($message->getMailedUserId());
        $gift = $this->giftRepository->find($message->getGiftId());
        $creator = $this->userRepository->find($message->getCreatorId());
        if (null === $mailedUser || null === $gift || null === $creator) {
            return;
        }
        $this->mailer->sendCreationAlertMessage($mailedUser, $gift, $creator, $message->getHomeUrl());
    }
}
