<?php

namespace Spyck\AutomationBundle\Parameter;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Validator;

class DayParameterList implements ParameterListInterface
{
    #[Validator\NotNull]
    #[Validator\Type(DateTimeInterface::class)]
    private DateTimeInterface $date;

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getDateForQueryBuilder(): ?string
    {
        return $this->getDate()->format('Y-m-d');
    }

    public function setDate(DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getData(): array
    {
        return [
            'date' => $this->getDateForQueryBuilder(),
        ];
    }
}