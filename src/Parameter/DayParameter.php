<?php

namespace Spyck\AutomationBundle\Parameter;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Validator;

final class DayParameter implements ParameterInterface
{
    #[Validator\NotNull]
    #[Validator\Type(type: DateTimeImmutable::class)]
    private DateTimeImmutable $date;

    public function __construct()
    {
        $date = new DateTimeImmutable();

        $this->setDate($date);
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getDateForQueryBuilder(): ?string
    {
        return $this->getDate()->format('Y-m-d');
    }

    public function setDate(DateTimeImmutable $date): static
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