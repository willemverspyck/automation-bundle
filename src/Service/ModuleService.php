<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Service;

use Countable;
use Exception;
use IteratorAggregate;
use Spyck\AutomationBundle\Entity\ModuleInterface as Module;
use Spyck\AutomationBundle\ModuleInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

readonly class ModuleService
{
    /**
     * @param Countable&IteratorAggregate $modules
     */
    public function __construct(#[TaggedIterator(tag: 'spyck.automation.task.module')] private iterable $modules)
    {
    }

    /**
     * @throws Exception
     */
    public function getModuleByName(string $name): ModuleInterface
    {
        foreach ($this->modules->getIterator() as $module) {
            if (get_class($module) === $name) {
                return $module;
            }
        }

        throw new Exception(sprintf('Module "%s" not found', $name));
    }

    /**
     * @throws Exception
     */
    public function getModuleInstance(Module $module): ModuleInterface
    {
        $adapter = $module->getAdapter();

        $moduleInstance = $this->getModuleByName($adapter);
        $moduleInstance->setModule($module);

        return $moduleInstance;
    }
}
