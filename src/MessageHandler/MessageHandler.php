<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\MessageHandler;

use Exception;
use Spyck\AutomationBundle\Message\MessageInterface;
use Spyck\AutomationBundle\Message\ModuleMessageInterface;
use Spyck\AutomationBundle\Repository\ModuleRepository;
use Spyck\AutomationBundle\Service\JobService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler]
final class MessageHandler
{
    public function __construct(private readonly JobService $jobService, private readonly ModuleRepository $moduleRepository)
    {
    }

    /**
     * @throws Exception
     * @throws UnrecoverableMessageHandlingException
     */
    public function __invoke(ModuleMessageInterface $moduleMessage): void
    {
        $id = $moduleMessage->getModule()->getId();

        $module = $this->moduleRepository->getModuleById($id);

        if (null === $module) {
            throw new UnrecoverableMessageHandlingException(sprintf('Module "%s" not found', $id));
        }

        $job = $this->jobService->getJobByModule($module);

        if (false === $job instanceof MessageInterface) {
            throw new UnrecoverableMessageHandlingException(sprintf('Job must be instance of "%s"', MessageInterface::class));
        }

        $job->executeAutomationMessage($moduleMessage->getParameter());
    }
}
