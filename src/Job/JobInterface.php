<?php

namespace Spyck\AutomationBundle\Job;

use Spyck\AutomationBundle\Entity\ModuleInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: ['spyck.automation.job'])]
interface JobInterface
{
    public static function getIndexName(): string;

    public function getAutomationModule(): ModuleInterface;

    public function setAutomationModule(ModuleInterface $module): void;
}
