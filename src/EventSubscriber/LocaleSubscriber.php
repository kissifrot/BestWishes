<?php

namespace BestWishes\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    private string $defaultLocale;

    public function __construct(string $defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(RequestEvent $event): void
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

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 15]],
        ];
    }
}
