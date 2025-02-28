<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Message;

final class TaskMessage implements TaskMessageInterface
{
    private int $moduleId;
    private array $variables;

    public function getModuleId(): int
    {
        return $this->moduleId;
    }

    public function setModuleId(int $moduleId): self
    {
        $this->moduleId = $moduleId;

        return $this;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): self
    {
        $this->variables = $variables;

        return $this;
    }
}
