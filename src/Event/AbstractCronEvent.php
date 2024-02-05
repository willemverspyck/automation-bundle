<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Event;

use Spyck\AutomationBundle\Cron\CronInterface;
use Spyck\AutomationBundle\Parameter\ParameterInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractCronEvent extends Event
{
    public function __construct(private readonly CronInterface $cron, private readonly ParameterInterface $parameter)
    {
    }

    public function getCron(): CronInterface
    {
        return $this->cron;
    }

    public function getParameter(): ParameterInterface
    {
        return $this->parameter;
    }
}
