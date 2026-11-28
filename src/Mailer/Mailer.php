<?php

namespace BestWishes\Mailer;

use BestWishes\Entity\Gift;
use BestWishes\Entity\User;
use BestWishes\Security\Core\BestWishesSecurityContext;
use BestWishes\Security\Voter\GiftListVoter;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Mailer
{
    public function __construct(
        private readonly MailerInterface           $mailer,
        private readonly UrlGeneratorInterface     $router,
        private readonly TranslatorInterface       $translator,
        private readonly string                    $siteName,
        private readonly string                    $fromAddress,
        private readonly BestWishesSecurityContext $securityContext,
    ) {
    }

    public function sendPurchaseAlertMessage(User $user, Gift $purchasedGift, UserInterface $buyer): void
    {
        if (!$this->canSendAlert($user, $purchasedGift)) {
            return;
        }

        $templatedEmail = $this->renderMailTemplate(
            'mail.alert_purchase.title',
            'emails/alert_purchase.txt.twig',
            $user,
            [
                'buyer' => $buyer,
                'purchasedGift' => $purchasedGift,
            ]
        );
        $this->sendEmailMessage($templatedEmail, $this->fromAddress, $user->getEmail());
    }

    public function sendCreationAlertMessage(User $user, Gift $createdGift, UserInterface $creator): void
    {
        if (!$this->canSendAlert($user, $createdGift)) {
            return;
        }

        $templatedEmail = $this->renderMailTemplate(
            'mail.alert_creation.title',
            'emails/alert_creation.txt.twig',
            $user,
            [
                'creator' => $creator,
                'createdGift' => $createdGift,
            ]
        );
        $this->sendEmailMessage($templatedEmail, $this->fromAddress, $user->getEmail());
    }

    public function sendEditionAlertMessage(User $user, Gift $editedGift, UserInterface $editor): void
    {
        if (!$this->canSendAlert($user, $editedGift)) {
            return;
        }

        $templatedEmail = $this->renderMailTemplate(
            'mail.alert_edition.title',
            'emails/alert_edition.txt.twig',
            $user,
            [
                'editor' => $editor,
                'editedGift' => $editedGift,
            ]
        );
        $this->sendEmailMessage($templatedEmail, $this->fromAddress, $user->getEmail());
    }

    public function sendDeletionAlertMessage(User $user, Gift $deletedGift, UserInterface $deleter): void
    {
        if (!$this->canSendAlert($user, $deletedGift)) {
            return;
        }

        $templatedEmail = $this->renderMailTemplate(
            'mail.alert_deletion.title',
            'emails/alert_deletion.txt.twig',
            $user,
            [
                'deleter' => $deleter,
                'deletedGift' => $deletedGift,
            ]
        );
        $this->sendEmailMessage($templatedEmail, $this->fromAddress, $user->getEmail());
    }

    private function canSendAlert(User $user, Gift $gift): bool
    {
        return !$gift->isSurprise()
            || $this->securityContext->isGranted(GiftListVoter::VIEW_SURPRISES, $gift->getList(), $user);
    }

    /**
     * @param array<mixed> $data
     */
    private function renderMailTemplate(string $subjectTrans, string $templateFile, User $user, array $data): TemplatedEmail
    {
        $subject = $this->translator->trans($subjectTrans, ['%siteName%' => $this->siteName]);
        $data = array_merge($data, [
            'home' => $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'user' => $user,
        ]);

        return (new TemplatedEmail())->subject($subject)->textTemplate($templateFile)->context($data);
    }

    protected function sendEmailMessage(TemplatedEmail $renderedTemplate, string $fromEmail, string $toEmail): void
    {
        $renderedTemplate
            ->from($fromEmail)
            ->to($toEmail);

        $this->mailer->send($renderedTemplate);
    }
}
