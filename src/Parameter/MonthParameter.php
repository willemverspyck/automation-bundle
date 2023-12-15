<?php

namespace Spyck\AutomationBundle\Parameter;

use Symfony\Component\Validator\Constraints as Validator;

final class MonthParameter implements ParameterInterface
{
    #[Validator\NotNull]
    private int $year;

    #[Validator\NotNull]
    private int $month;

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function setMonth(int $month): static
    {
        $this->month = $month;

        return $this;
    }

    public function getData(): array
    {
        return [
            'year' => $this->getYear(),
            'month' => $this->getMonth(),
        ];
    }
}