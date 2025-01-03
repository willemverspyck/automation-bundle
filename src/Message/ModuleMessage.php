<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Message;

use Spyck\AutomationBundle\Parameter\ParameterInterface;

final class ModuleMessage implements ModuleMessageInterface
{
    private int $id;
    private ParameterInterface $parameter;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getParameter(): ParameterInterface
    {
        return $this->parameter;
    }

    public function setParameter(ParameterInterface $parameter): self
    {
        $this->parameter = $parameter;

        return $this;
    }
}
