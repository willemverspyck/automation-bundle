<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Service;

use Exception;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Job\JobInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

readonly class JobService
{
    public function __construct(#[AutowireLocator(services: 'spyck.automation.job', defaultIndexMethod: 'getIndexName')] private ServiceLocator $serviceLocator)
    {
    }

    public function getJob(string $name): JobInterface
    {
        return $this->serviceLocator->get($name);
    }

    /**
     * @throws Exception
     */
    public function getJobByModule(ModuleInterface $module): JobInterface
    {
        $adapter = $module->getAdapter();

        if (null === $adapter) {
            throw new Exception(sprintf('Module "%s" has no adapter', $module->getName()));
        }

        $job = $this->getJob($adapter);
        $job->setAutomationModule($module);

        return $job;
    }
}
