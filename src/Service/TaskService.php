<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Service;

use Exception;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Entity\ScheduleInterface;
use Spyck\AutomationBundle\Event\PostTaskEvent;
use Spyck\AutomationBundle\Event\PreTaskEvent;
use Spyck\AutomationBundle\Exception\ParameterException;
use Spyck\AutomationBundle\Message\TaskMessage;
use Spyck\AutomationBundle\Repository\TaskRepository;
use Spyck\AutomationBundle\Task\TaskInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class TaskService
{
    public function __construct(private EventDispatcherInterface $eventDispatcher, private JobService $jobService, private MapService $mapService, private MessageBusInterface $messageBus, private TaskRepository $taskRepository, private ValidatorInterface $validator)
    {
        ini_set('memory_limit', '4G');
    }

    /**
     * @throws Exception
     */
    public function executeTask(ModuleInterface $module, array $variables): void
    {
        $job = $this->jobService->getJobByModule($module);

        if (false === $job instanceof TaskInterface) {
            throw new Exception(sprintf('"%s" is no instance of TaskInterface', get_class($job)));
        }

        $parameter = $job->getAutomationTaskParameter();

        $map = $this->mapService->getMap($variables, $parameter);

        $constraintViolationList = $this->validator->validate($map);

        if ($constraintViolationList->count() > 0) {
            $constraintViolation = $constraintViolationList->offsetGet(0);

            throw new ParameterException(sprintf('%s', $constraintViolation->getMessage()));
        }

        $preCronEvent = new PreTaskEvent($job, $map);

        $this->eventDispatcher->dispatch($preCronEvent);

        $job->executeAutomationTask($map);

        $postCronEvent = new PostTaskEvent($job, $map);

        $this->eventDispatcher->dispatch($postCronEvent);
    }

    public function executeTaskAsMessage(int $id, array $variables = []): void
    {
        $taskMessage = new TaskMessage();
        $taskMessage->setId($id);
        $taskMessage->setVariables($variables);

        $this->messageBus->dispatch($taskMessage);
    }

    public function executeTaskAsMessageBySchedule(ScheduleInterface $schedule): void
    {
        $tasks = $this->taskRepository->getTasksBySchedule($schedule);

        foreach ($tasks as $task) {
            $this->executeTaskAsMessage($task->getModule()->getId(), $task->getVariables());
        }
    }
}
