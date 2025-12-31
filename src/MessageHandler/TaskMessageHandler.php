<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\MessageHandler;

use Exception;
use Spyck\AutomationBundle\Message\TaskMessageInterface;
use Spyck\AutomationBundle\Repository\ModuleRepository;
use Spyck\AutomationBundle\Repository\TaskRepository;
use Spyck\AutomationBundle\Service\TaskService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler(sign: true)]
final class TaskMessageHandler
{
    public function __construct(private readonly ModuleRepository $moduleRepository, private readonly TaskRepository $taskRepository, private readonly TaskService $taskService)
    {
    }

    /**
     * @throws Exception
     * @throws UnrecoverableMessageHandlingException
     */
    public function __invoke(TaskMessageInterface $taskMessage): void
    {
        $moduleId = $taskMessage->getModuleId();

        $module = $this->moduleRepository->getModuleById($moduleId);

        if (null === $module) {
            throw new UnrecoverableMessageHandlingException(sprintf('Module "%s" not found', $moduleId));
        }

        $this->taskService->executeTask($module, $taskMessage->getVariables());
    }
}
