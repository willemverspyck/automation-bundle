<?php

namespace Spyck\AutomationBundle\Cron;

use Exception;
use Spyck\AutomationBundle\Entity\Cron;
use Spyck\AutomationBundle\Job\JobInterface;
use Spyck\AutomationBundle\Parameter\ParameterInterface;

interface CronInterface extends JobInterface
{
    /**
     * @throws Exception
     */
    public function executeAutomationCron(string $callback, ParameterInterface $parameter): void;

    public function getAutomationCronParameter(): string;

    public function getAutomationCronCallbacks(): iterable;

    public function getAutomationCron(): Cron;

    public function setAutomationCron(Cron $cron): void;
}
