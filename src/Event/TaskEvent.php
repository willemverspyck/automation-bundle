<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Event;

use Spyck\AutomationBundle\Entity\ModuleInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class TaskEvent extends Event
{
    public function __construct(private readonly ModuleInterface $module, private readonly array $variables)
    {
    }

    public function getModule(): ModuleInterface
    {
        return $this->module;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }
}
