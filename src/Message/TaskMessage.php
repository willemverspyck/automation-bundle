<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Message;

final class TaskMessage implements TaskMessageInterface
{
    private int $id;
    private array $variables;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

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
