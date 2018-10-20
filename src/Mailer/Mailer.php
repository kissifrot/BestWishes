<?php

namespace BestWishes\Mailer;

use BestWishes\Entity\Gift;
use BestWishes\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Mailer
{
    private $mailer;
    private $router;
    private $templating;
    private $fromAddress;

    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface $router, EngineInterface $templating, string $fromAddress)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->fromAddress = $fromAddress;
    }

    public function sendPurchaseAlertMessage(User $user, Gift $purchasedGift, UserInterface $buyer): void
    {
        $templateFile = 'emails/alert_purchase.txt.twig';
        $home = $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render($templateFile, array(
            'user' => $user,
            'buyer' => $buyer,
            'purchasedGift' => $purchasedGift,
            'home' => $home,
        ));
        $this->sendEmailMessage($rendered, $this->fromAddress, (string) $user->getEmail());
    }

    public function sendCreationAlertMessage(User $user, Gift $createdGift, UserInterface $creator): void
    {
        $templateFile = 'emails/alert_creation.txt.twig';
        $home = $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render($templateFile, array(
            'user' => $user,
            'creator' => $creator,
            'createdGift' => $createdGift,
            'home' => $home,
        ));
        $this->sendEmailMessage($rendered, $this->fromAddress, (string) $user->getEmail());
    }

    public function sendEditionAlertMessage(User $user, Gift $editedGift, UserInterface $editor): void
    {
        $templateFile = 'emails/alert_edition.txt.twig';
        $home = $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render($templateFile, array(
            'user' => $user,
            'editor' => $editor,
            'editedGift' => $editedGift,
            'home' => $home,
        ));
        $this->sendEmailMessage($rendered, $this->fromAddress, (string) $user->getEmail());
    }

    public function sendDeletionAlertMessage(User $user, Gift $deletedGift, UserInterface $deleter): void
    {
        $templateFile = 'emails/alert_deletion.txt.twig';
        $home = $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render($templateFile, array(
            'user' => $user,
            'deleter' => $deleter,
            'deletedGift' => $deletedGift,
            'home' => $home,
        ));
        $this->sendEmailMessage($rendered, $this->fromAddress, (string) $user->getEmail());
    }

    /**
     * @param string $renderedTemplate
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail): void
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = array_shift($renderedLines);
        $body = implode("\n", $renderedLines);

        $message = new \Swift_Message($subject, $body);
        $message
            ->setFrom($fromEmail)
            ->setTo($toEmail);

        $this->mailer->send($message);
    }
}
