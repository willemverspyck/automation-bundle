<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Service;

use App\Repository\ModuleRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Exception\ParameterException;
use Spyck\AutomationBundle\TaskInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class TaskService
{
    public function __construct(private LoggerInterface $logger, private MapService $mapService, private ModuleRepository $moduleRepository, private ModuleService $moduleService, private ValidatorInterface $validator)
    {
        ini_set('memory_limit', '4G');
    }

    /**
     * @throws Exception
     */
    public function executeTaskByModuleId(int $moduleId, array $parameters = []): void
    {
        $module = $this->moduleRepository->getModuleById($moduleId);

        if (null === $module) {
            $this->logger->error('Module not found', [
                'moduleId' => $moduleId,
                'parameters' => $parameters,
            ]);

            return;
        }

        $this->executeTask($module, $parameters);
    }

    /**
     * @throws Exception
     */
    private function executeTask(ModuleInterface $module, array $parameters): void
    {
        $moduleInstance = $this->moduleService->getModuleInstance($module);

        if ($moduleInstance instanceof TaskInterface) {
            $parameter = $this->mapService->getMap($parameters);

            $constraintViolationList = $this->validator->validate($parameter);
            if ($constraintViolationList->count() > 0) {
                $constraintViolation = $constraintViolationList->offsetGet(0);

                throw new ParameterException(sprintf('%s', $constraintViolation->getMessage()));
            }

            $moduleInstance->executeAutomationTask($parameter);

            return;
        }

        throw new Exception(sprintf('"%s" is no instance of TaskInterface', get_class($moduleInstance)));
    }
}
