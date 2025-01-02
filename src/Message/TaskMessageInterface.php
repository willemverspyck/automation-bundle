<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Message;

interface TaskMessageInterface
{
    public function getId(): int;

    public function setId(int $id): self;

    public function getVariables(): array;

    public function setVariables(array $variables): self;
}
