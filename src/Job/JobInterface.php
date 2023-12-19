<?php

namespace Spyck\AutomationBundle\Job;

use App\Entity\Module;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Spyck\AutomationBundle\Entity\ModuleInterface;

#[Autoconfigure(tags: ['spyck.automation.job'])]
interface JobInterface
{
    public function getAutomationModule(): ModuleInterface;

    public function setAutomationModule(ModuleInterface $module): void;
}
