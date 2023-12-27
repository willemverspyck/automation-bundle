<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Service;

use App\Repository\ModuleRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Exception\ParameterException;
use Spyck\AutomationBundle\Task\TaskInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class TaskService
{
    public function __construct(private JobService $jobService, private LoggerInterface $logger, private MapService $mapService, private ModuleRepository $moduleRepository, private ValidatorInterface $validator)
    {
        ini_set('memory_limit', '4G');
    }

    /**
     * @throws Exception
     */
    public function executeTaskByModuleId(int $moduleId, array $variables = []): void
    {
        $module = $this->moduleRepository->getModuleById($moduleId);

        if (null === $module) {
            $this->logger->error('Module not found', [
                'moduleId' => $moduleId,
                'variables' => $variables,
            ]);

            return;
        }

        $this->executeTask($module, $variables);
    }

    /**
     * @throws Exception
     */
    private function executeTask(ModuleInterface $module, array $variables): void
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

        $job->executeAutomationTask($map);
    }
}
