<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Event;

use Spyck\AutomationBundle\Parameter\ParameterInterface;
use Spyck\AutomationBundle\Task\TaskInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractTaskEvent extends Event
{
    public function __construct(private readonly TaskInterface $task, private readonly ParameterInterface $parameter)
    {
    }

    public function getTask(): TaskInterface
    {
        return $this->task;
    }

    public function getParameter(): ParameterInterface
    {
        return $this->parameter;
    }
}
