<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Entity;

interface ModuleInterface
{
    public function getId(): ?int;

    public function getAdapter(): ?string;
}
