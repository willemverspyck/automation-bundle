<?php

namespace Spyck\AutomationBundle\Module;

use App\Entity\Module;
use Exception;
use Spyck\AutomationBundle\Entity\Cron;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Parameter\ParameterInterface;
use Spyck\AutomationBundle\Repository\CronRepository;
use Symfony\Contracts\Service\Attribute\Required;

trait ModuleTrait
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
