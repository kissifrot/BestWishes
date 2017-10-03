<?php

namespace AppBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            $prefLanguage = $request->getPreferredLanguage();
            // Convert to ISO 3166-2 equivalent language when possible
            switch ($prefLanguage) {
                case 'fr':
                case 'fr_FR':
                    $locale = 'fr';
                    break;
                case 'en_US':
                case 'en_GB':
                default:
                    $locale = $this->defaultLocale;
            }
            $request->setLocale($request->getSession()->get('_locale', $locale));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 15)),
        );
    }
}
