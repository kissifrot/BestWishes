<?php

namespace AppBundle\Mailer;


use AppBundle\Entity\Gift;
use AppBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var UrlGeneratorInterface
     */
    private $router;
    /**
     * @var EngineInterface
     */
    private $templating;
    /**
     * @var string
     */
    private $fromAddress;

    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface $router, EngineInterface $templating, $fromAddress)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->fromAddress = $fromAddress;
    }

    public function sendPurchaseAlertMessage(User $user, Gift $purchasedGift)
    {
        $templateFile = 'AppBundle:emails:alert_purchase.txt.twig';
        $home = $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render($templateFile, array(
            'user' => $user,
            'purchasedGift' => $purchasedGift,
            'home' => $home,
        ));
        $this->sendEmailMessage($rendered, $this->fromAddress, (string) $user->getEmail());
    }

    public function sendCreationAlertMessage(User $user, Gift $createdGift)
    {
        $templateFile = 'AppBundle:emails:alert_creation.txt.twig';
        $home = $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render($templateFile, array(
            'user' => $user,
            'createdGift' => $createdGift,
            'home' => $home,
        ));
        $this->sendEmailMessage($rendered, $this->fromAddress, (string) $user->getEmail());
    }

    public function sendEditionAlertMessage(User $user, Gift $editeddGift)
    {
        $templateFile = 'AppBundle:emails:alert_edition.txt.twig';
        $home = $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->templating->render($templateFile, array(
            'user' => $user,
            'editeddGift' => $editeddGift,
            'home' => $home,
        ));
        $this->sendEmailMessage($rendered, $this->fromAddress, (string) $user->getEmail());
    }

    /**
     * @param string $renderedTemplate
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail)
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = array_shift($renderedLines);
        $body = implode("\n", $renderedLines);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body);

        $this->mailer->send($message);
    }
}
