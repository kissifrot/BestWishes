<?php

namespace BestWishes\Mailer;

use BestWishes\Entity\Gift;
use BestWishes\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class Mailer
{
    private $mailer;
    private $router;
    private $translator;
    private $siteName;
    private $fromAddress;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $router, TranslatorInterface $translator, string $siteName, string $fromAddress)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->translator = $translator;
        $this->siteName = $siteName;
        $this->fromAddress = $fromAddress;
    }

    public function sendPurchaseAlertMessage(User $user, Gift $purchasedGift, UserInterface $buyer): void
    {
        $templatedEmail = $this->renderMailTemplate('mail.alert_creation.title', 'emails/alert_purchase.txt.twig', $user, [
            'buyer' => $buyer,
            'purchasedGift' => $purchasedGift,
        ]);
        $this->sendEmailMessage($templatedEmail, $this->fromAddress, $user->getEmail());
    }

    public function sendCreationAlertMessage(User $user, Gift $createdGift, UserInterface $creator): void
    {
        $templatedEmail = $this->renderMailTemplate('mail.alert_purchase.title', 'emails/alert_creation.txt.twig', $user, [
            'creator' => $creator,
            'createdGift' => $createdGift,
        ]);
        $this->sendEmailMessage($templatedEmail, $this->fromAddress, $user->getEmail());
    }

    public function sendEditionAlertMessage(User $user, Gift $editedGift, UserInterface $editor): void
    {
        $templatedEmail = $this->renderMailTemplate('mail.alert_edition.title', 'emails/alert_edition.txt.twig', $user, [
            'editor' => $editor,
            'editedGift' => $editedGift
        ]);
        $this->sendEmailMessage($templatedEmail, $this->fromAddress, $user->getEmail());
    }

    public function sendDeletionAlertMessage(User $user, Gift $deletedGift, UserInterface $deleter): void
    {
        $templatedEmail = $this->renderMailTemplate('mail.alert_deletion.title', 'emails/alert_deletion.txt.twig', $user, [
            'deleter' => $deleter,
            'deletedGift' => $deletedGift
        ]);
        $this->sendEmailMessage($templatedEmail, $this->fromAddress, $user->getEmail());
    }

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
