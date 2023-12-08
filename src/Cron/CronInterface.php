<?php

namespace Spyck\AutomationBundle\Cron;

use Exception;
use Spyck\AutomationBundle\Entity\Cron;
use Spyck\AutomationBundle\Parameter\ParameterListInterface;

interface CronInterface
{
    /**
     * @throws Exception
     */
    public function executeAutomationCron(string $callback, ParameterListInterface $parameter): void;

    public function getAutomationCronCallbacks(): iterable;

    public function getAutomationCron(): Cron;

    public function setAutomationCron(Cron $cron): void;
}
