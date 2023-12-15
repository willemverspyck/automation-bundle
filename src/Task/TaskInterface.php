<?php

namespace Spyck\AutomationBundle\Task;

use Spyck\AutomationBundle\Parameter\ParameterInterface;

interface TaskInterface
{
    public function executeAutomationTask(ParameterInterface $parameter): void;

    public function getAutomationTaskParameter(): string;
}
