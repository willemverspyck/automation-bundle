<?php

namespace Spyck\AutomationBundle\Parameter;

use DateTime;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Validator;

final class DayParameter implements ParameterInterface
{
    #[Validator\NotNull]
    #[Validator\Type(type: DateTimeInterface::class)]
    private DateTimeInterface $date;

    public function __construct()
    {
        $date = new DateTime();

        $this->setDate($date);
    }

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