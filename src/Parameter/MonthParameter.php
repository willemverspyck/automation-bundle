<?php

namespace Spyck\AutomationBundle\Parameter;

use DateTime;
use Symfony\Component\Validator\Constraints as Validator;

final class MonthParameter implements ParameterInterface
{
    #[Validator\NotNull]
    #[Validator\Type(type: 'integer')]
    private int $year;

    #[Validator\NotNull]
    #[Validator\Range(min: 1, max: 12)]
    #[Validator\Type(type: 'integer')]
    private int $month;

    public function __construct()
    {
        $date = new DateTime();

        $this->setYear((int) $date->format('Y'));
        $this->setMonth((int) $date->format('n'));
    }

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