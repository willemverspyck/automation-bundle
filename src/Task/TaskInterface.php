<?php

namespace Spyck\AutomationBundle\Task;

use Spyck\AutomationBundle\Job\JobInterface;
use Spyck\AutomationBundle\Parameter\ParameterInterface;

interface TaskInterface extends JobInterface
{
    public function executeAutomationTask(ParameterInterface $parameter): void;

    public function getAutomationTaskParameter(): string;
}
