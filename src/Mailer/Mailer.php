<?php

namespace BestWishes\Mailer;

use BestWishes\Entity\Gift;
use BestWishes\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

class Mailer
{
    private $mailer;
    private $router;
    private $twig;
    private $fromAddress;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $router, Environment $twig, string $fromAddress)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
        $this->fromAddress = $fromAddress;
    }

    public function sendPurchaseAlertMessage(User $user, Gift $purchasedGift, UserInterface $buyer): void
    {
        $rendered = $this->renderMailTemplate('emails/alert_purchase.txt.twig', $user, [
            'buyer' => $buyer,
            'purchasedGift' => $purchasedGift,
        ]);
        $this->sendEmailMessage($rendered, $this->fromAddress, (string) $user->getEmail());
    }

    public function sendCreationAlertMessage(User $user, Gift $createdGift, UserInterface $creator): void
    {
        $rendered = $this->renderMailTemplate('emails/alert_creation.txt.twig', $user, [
            'creator' => $creator,
            'createdGift' => $createdGift,
        ]);
        $this->sendEmailMessage($rendered, $this->fromAddress, (string) $user->getEmail());
    }

    public function sendEditionAlertMessage(User $user, Gift $editedGift, UserInterface $editor): void
    {
        $rendered = $this->renderMailTemplate('emails/alert_edition.txt.twig', $user, [
            'editor' => $editor,
            'editedGift' => $editedGift
        ]);
        $this->sendEmailMessage($rendered, $this->fromAddress, (string) $user->getEmail());
    }

    public function sendDeletionAlertMessage(User $user, Gift $deletedGift, UserInterface $deleter): void
    {
        $rendered = $this->renderMailTemplate('emails/alert_deletion.txt.twig', $user, [
            'deleter' => $deleter,
            'deletedGift' => $deletedGift
        ]);
        $this->sendEmailMessage($rendered, $this->fromAddress, (string) $user->getEmail());
    }

    private function renderMailTemplate(string $templateFile, User $user, array $data): string
    {
        $data = array_merge($data, [
            'home' => $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'user' => $user,
        ]);
        return $this->twig->render($templateFile, $data);
    }

    protected function sendEmailMessage(string $renderedTemplate, string $fromEmail, string $toEmail): void
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = array_shift($renderedLines);
        $body = implode("\n", $renderedLines);

        $email = (new Email())
            ->subject($subject)
            ->text($body)
            ->from($fromEmail)
            ->to($toEmail);

        $this->mailer->send($email);
    }
}
