<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Event\Subscriber;

use Spyck\AutomationBundle\Event\TaskEvent;
use Spyck\AutomationBundle\Service\TaskService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class TaskEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TaskService $taskService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TaskEvent::class => [
                'onTask',
            ],
        ];
    }

    public function onTask(TaskEvent $event): void
    {
        $module = $event->getModule();
        $variables = $event->getVariables();

        $this->taskService->executeTaskAsMessage($module, $variables);
    }
}
