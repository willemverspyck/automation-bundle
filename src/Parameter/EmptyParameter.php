<?php

namespace Spyck\AutomationBundle\Parameter;

final class EmptyParameter implements ParameterInterface
{
    public function getData(): array
    {
        return [];
    }
}
