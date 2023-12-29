<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\MessageHandler;

use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Job\JobInterface;
use Spyck\AutomationBundle\Message\MessageInterface;
use Spyck\AutomationBundle\Message\ModuleMessageInterface;
use Spyck\AutomationBundle\Service\JobService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler]
final class MessageHandler
{
    public function __construct(private readonly JobService $jobService)
    {
    }

    /**
     * @throws Exception
     * @throws UnrecoverableMessageHandlingException
     */
    public function __invoke(ModuleMessageInterface $moduleMessage): void
    {
        $job = $this->jobService->getJobByModule($moduleMessage->getModule());

        if (false === $job instanceof MessageInterface) {
            throw new UnrecoverableMessageHandlingException(sprintf('Job must be instance of "%s"', MessageInterface::class));
        }

        $job->executeAutomationMessage($moduleMessage->getParameter());
    }
}
