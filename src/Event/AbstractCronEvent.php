<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Event;

use Spyck\AutomationBundle\Cron\CronInterface;
use Spyck\AutomationBundle\Entity\Cron;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Parameter\ParameterInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractCronEvent extends Event
{
    public function __construct(private readonly CronInterface $job, private readonly ParameterInterface $parameter)
    {
    }

    public function getModule(): ModuleInterface
    {
        return $this->job->getAutomationModule();
    }

    public function getCron(): Cron
    {
        return $this->job->getAutomationCron();
    }

    public function getParameter(): ParameterInterface
    {
        return $this->parameter;
    }
}
