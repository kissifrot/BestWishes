<?php

namespace BestWishes\Mailer;

use BestWishes\Entity\Gift;
use BestWishes\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Mailer
{
    public function __construct(
        private readonly MailerInterface       $mailer,
        private readonly TranslatorInterface   $translator,
        private readonly string                $siteName,
        private readonly string                $fromAddress
    ) {
    }

    public function sendPurchaseAlertMessage(User $user, Gift $purchasedGift, UserInterface $buyer, string $homeUrl): void
    {
        $templatedEmail = $this->renderMailTemplate(
            'mail.alert_purchase.title',
            'emails/alert_purchase.txt.twig',
            $user,
            $homeUrl,
            [
                'home' => $homeUrl,
                'buyer' => $buyer,
                'purchasedGift' => $purchasedGift,
            ]
        );
        $this->sendEmailMessage($templatedEmail, $this->fromAddress, $user->getEmail());
    }

    public function sendCreationAlertMessage(User $user, Gift $createdGift, UserInterface $creator, string $homeUrl): void
    {
        $templatedEmail = $this->renderMailTemplate(
            'mail.alert_creation.title',
            'emails/alert_creation.txt.twig',
            $user,
            $homeUrl,
            [
                'home' => $homeUrl,
                'creator' => $creator,
                'createdGift' => $createdGift,
            ]
        );
        $this->sendEmailMessage($templatedEmail, $this->fromAddress, $user->getEmail());
    }

    public function sendEditionAlertMessage(User $user, Gift $editedGift, UserInterface $editor, string $homeUrl): void
    {
        $templatedEmail = $this->renderMailTemplate(
            'mail.alert_edition.title',
            'emails/alert_edition.txt.twig',
            $user,
            $homeUrl,
            [
                'editor' => $editor,
                'editedGift' => $editedGift
            ]
        );
        $this->sendEmailMessage($templatedEmail, $this->fromAddress, $user->getEmail());
    }

    public function sendDeletionAlertMessage(User $user, Gift $deletedGift, UserInterface $deleter, string $homeUrl): void
    {
        $templatedEmail = $this->renderMailTemplate(
            'mail.alert_deletion.title',
            'emails/alert_deletion.txt.twig',
            $user,
            $homeUrl,
            [
                'deleter' => $deleter,
                'deletedGift' => $deletedGift
            ]
        );
        $this->sendEmailMessage($templatedEmail, $this->fromAddress, $user->getEmail());
    }

    /**
     * @param array<mixed> $data
     */
    private function renderMailTemplate(string $subjectTrans, string $templateFile, User $user, string $homeUrl, array $data): TemplatedEmail
    {
        $subject = $this->translator->trans($subjectTrans, ['%siteName%' => $this->siteName]);
        $data = array_merge($data, [
            'home' => $homeUrl,
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
