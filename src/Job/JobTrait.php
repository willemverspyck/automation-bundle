<?php

namespace Spyck\AutomationBundle\Job;

use Spyck\AutomationBundle\Entity\ModuleInterface;

trait JobTrait
{
    private ModuleInterface $module;

    public function setAutomationModule(ModuleInterface $module): void
    {
        $this->module = $module;
    }

    public function getAutomationModule(): ModuleInterface
    {
        return $this->module;
    }
}
