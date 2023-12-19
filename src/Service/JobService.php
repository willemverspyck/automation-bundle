<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Service;

use Countable;
use Exception;
use IteratorAggregate;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Job\JobInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

readonly class JobService
{
    /**
     * @param Countable&IteratorAggregate $modules
     */
    public function __construct(#[TaggedIterator(tag: 'spyck.automation.job')] private iterable $modules)
    {
    }

    /**
     * @throws Exception
     */
    public function getJobByModule(ModuleInterface $module): JobInterface
    {
        $adapter = $module->getAdapter();

        $job = $this->getJob($adapter);
        $job->setAutomationModule($module);

        return $job;
    }

    /**
     * @throws Exception
     */
    private function getJob(string $name): JobInterface
    {
        foreach ($this->modules->getIterator() as $module) {
            if (get_class($module) === $name) {
                return $module;
            }
        }

        throw new Exception(sprintf('Module "%s" not found', $name));
    }
}
