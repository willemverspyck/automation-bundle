<?php

namespace Spyck\AutomationBundle;

use Spyck\AutomationBundle\Parameter\ParameterListInterface;

interface TaskInterface
{
    public function executeAutomationTask(ParameterListInterface $parameter): void;
}
