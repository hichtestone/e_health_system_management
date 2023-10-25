<?php

declare(strict_types=1);

namespace App\ETMF\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

/**
 * Class MaintenanceModeSubscriber.
 */
class MaintenanceModeSubscriber implements EventSubscriberInterface
{
    /** @var Environment */
    private $templating;
    /** @var int */
    private $maintenance_mode;

    /**
     * KernelSubscriber constructor.
     */
    public function __construct(int $maintenance_mode, Environment $templating)
    {
        $this->templating = $templating;
        $this->maintenance_mode = $maintenance_mode;
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 19]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // mode maintenance
        if (1 === $this->maintenance_mode) {
            $view = $this->templating->render('app/maintenance.html.twig');
            $event->setResponse(new Response($view));
            $event->stopPropagation();

            return;
        }
    }
}
