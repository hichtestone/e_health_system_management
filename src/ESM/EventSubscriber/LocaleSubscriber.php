<?php

declare(strict_types=1);

namespace App\ESM\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class LocaleSubscriber.
 */
class LocaleSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $defaultLocale;

    public function __construct(string $default_lang = 'fr')
    {
        $this->defaultLocale = $default_lang;
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $locale = $session->get('_locale', $request->cookies->get('language', $this->defaultLocale));
        $request->setLocale($locale);
    }
}
