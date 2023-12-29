<?php

namespace Spyck\AutomationBundle\Message;

use Exception;
use Spyck\AutomationBundle\Job\JobInterface;
use Spyck\AutomationBundle\Parameter\ParameterInterface;

interface MessageInterface extends JobInterface
{
    /**
     * @throws Exception
     */
    public function executeAutomationMessage(ParameterInterface $parameter): void;
}
