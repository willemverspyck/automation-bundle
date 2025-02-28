<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Message;

interface TaskMessageInterface
{
    public function getModuleId(): int;

    public function setModuleId(int $moduleId): self;

    public function getVariables(): array;

    public function setVariables(array $variables): self;
}
