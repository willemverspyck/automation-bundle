<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Message;

use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Parameter\ParameterInterface;

final class ModuleMessage implements ModuleMessageInterface
{
    private ModuleInterface $module;
    private ParameterInterface $parameter;

    public function getModule(): ModuleInterface
    {
        return $this->module;
    }

    public function setModule(ModuleInterface $module): self
    {
        $this->module = $module;

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
