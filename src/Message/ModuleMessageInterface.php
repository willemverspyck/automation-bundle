<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Message;

use App\Entity\FollowInterface;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Parameter\ParameterInterface;

interface ModuleMessageInterface
{
    public function getModule(): ModuleInterface;

    public function setModule(ModuleInterface $module): self;

    public function getParameter(): ParameterInterface;

    public function setParameter(ParameterInterface $parameter): self;
}
