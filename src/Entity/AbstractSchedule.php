<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Doctrine;
use Spyck\AutomationBundle\Repository\ScheduleRepository;
use Stringable;
use Symfony\Component\Validator\Constraints as Validator;

#[Doctrine\Entity(repositoryClass: ScheduleRepository::class)]
#[Doctrine\InheritanceType(value: 'SINGLE_TABLE')]
#[Doctrine\DiscriminatorColumn(name: 'discriminator', type: Types::STRING, length: 128)]
#[Doctrine\DiscriminatorMap(value: [
    ScheduleForEvent::class => ScheduleForEvent::class,
    ScheduleForSystem::class => ScheduleForSystem::class,
])]
#[Doctrine\Table(name: 'automation_schedule')]
abstract class AbstractSchedule implements ScheduleInterface, Stringable, TimestampInterface
{
    use TimestampTrait;

    #[Doctrine\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[Doctrine\Id]
    #[Doctrine\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[Doctrine\Column(name: 'name', type: Types::STRING, length: 256)]
    #[Validator\NotNull]
    private string $name;

    #[Doctrine\Column(name: 'code', type: Types::STRING, length: 128, nullable: true)]
    private ?string $code;

    #[Doctrine\Column(name: 'match_hours', type: Types::JSON)]
    private array $matchHours;

    #[Doctrine\Column(name: 'match_days', type: Types::JSON)]
    private array $matchDays;

    #[Doctrine\Column(name: 'match_weeks', type: Types::JSON)]
    private array $matchWeeks;

    #[Doctrine\Column(name: 'match_weekdays', type: Types::JSON)]
    private array $matchWeekdays;

    #[Doctrine\Column(name: 'active', type: Types::BOOLEAN)]
    private bool $active;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getMatchHours(): array
    {
        return $this->matchHours;
    }

    public function setMatchHours(array $matchHours): static
    {
        $this->matchHours = $matchHours;

        return $this;
    }

    public function getMatchDays(): array
    {
        return $this->matchDays;
    }

    public function setMatchDays(array $matchDays): static
    {
        $this->matchDays = $matchDays;

        return $this;
    }

    public function getMatchWeeks(): array
    {
        return $this->matchWeeks;
    }

    public function setMatchWeeks(array $matchWeeks): static
    {
        $this->matchWeeks = $matchWeeks;

        return $this;
    }

    public function getMatchWeekdays(): array
    {
        return $this->matchWeekdays;
    }

    public function setMatchWeekdays(array $matchWeekdays): static
    {
        $this->matchWeekdays = $matchWeekdays;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->getName(), $this->getDiscriminator());
    }
}
