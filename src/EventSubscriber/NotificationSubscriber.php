<?php

// src/EventSubscriber/NotificationSubscriber.php


namespace App\EventSubscriber;
// src/EventSubscriber/NotificationSubscriber.php


use App\Repository\NotificationRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class NotificationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private Environment $twig
    ) {}

    public function onKernelController(ControllerEvent $event): void
    {
        $this->twig->addGlobal(
            'notifications_unread',
            $this->notificationRepository->findUnreadNotifications()
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}