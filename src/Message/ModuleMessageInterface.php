<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Message;

use Spyck\AutomationBundle\Parameter\ParameterInterface;

interface ModuleMessageInterface
{
    public function getId(): int;

    public function setId(int $id): self;

    public function getParameter(): ParameterInterface;

    public function setParameter(ParameterInterface $parameter): self;
}
