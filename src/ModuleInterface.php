<?php

namespace Spyck\AutomationBundle;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Spyck\AutomationBundle\Entity\ModuleInterface as BaseModuleInterface;

#[Autoconfigure(tags: ['spyck.automation.task.module'])]
interface ModuleInterface
{
    public function setModule(BaseModuleInterface $module): void;
}
