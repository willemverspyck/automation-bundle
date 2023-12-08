<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Doctrine;
use Stringable;

#[Doctrine\Table(name: 'automation_schedule')]
#[Doctrine\Entity]
class Schedule implements Stringable
{
    #[Doctrine\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[Doctrine\Id]
    #[Doctrine\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[Doctrine\Column(name: 'name', type: Types::STRING, length: 256)]
    private string $name;

    #[Doctrine\Column(name: 'hours', type: Types::JSON)]
    private array $hours;

    #[Doctrine\Column(name: 'days', type: Types::JSON)]
    private array $days;

    #[Doctrine\Column(name: 'weeks', type: Types::JSON)]
    private array $weeks;

    #[Doctrine\Column(name: 'weekdays', type: Types::JSON)]
    private array $weekdays;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getHours(): array
    {
        return $this->hours;
    }

    public function setHours(array $hours): static
    {
        $this->hours = $hours;

        return $this;
    }

    public function getDays(): array
    {
        return $this->days;
    }

    public function setDays(array $days): static
    {
        $this->days = $days;

        return $this;
    }

    public function getWeeks(): array
    {
        return $this->weeks;
    }

    public function setWeeks(array $weeks): static
    {
        $this->weeks = $weeks;

        return $this;
    }

    public function getWeekdays(): array
    {
        return $this->weekdays;
    }

    public function setWeekdays(array $weekdays): static
    {
        $this->weekdays = $weekdays;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
