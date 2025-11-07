<?php

namespace BestWishes\MessageHandler;

use BestWishes\Mailer\Mailer;
use BestWishes\Message\PurchaseAlertMessage;
use BestWishes\Repository\GiftRepository;
use BestWishes\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class PurchaseAlertMessageHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly GiftRepository $giftRepository,
        private readonly Mailer                    $mailer,
    ) {
    }

    public function __invoke(PurchaseAlertMessage $message): void
    {
        $mailedUser = $this->userRepository->find($message->getMailedUserId());
        $gift = $this->giftRepository->find($message->getGiftId());
        $buyer = $this->userRepository->find($message->getBuyerId());
        if (null === $mailedUser || null === $gift || null === $buyer) {
            return;
        }
        $this->mailer->sendPurchaseAlertMessage($mailedUser, $gift, $buyer, $message->getHomeUrl());
    }
}
