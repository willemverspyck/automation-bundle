<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Entity;

interface ScheduleInterface
{
    public function getId(): ?int;

    public function getName(): string;

    public function setName(string $name): static;

    public function getCode(): ?string;

    public function setCode(?string $code): static;

    public function getMatchHours(): array;

    public function setMatchHours(array $matchHours): static;

    public function getMatchDays(): array;

    public function setMatchDays(array $matchDays): static;

    public function getMatchWeeks(): array;

    public function setMatchWeeks(array $matchWeeks): static;

    public function getMatchWeekdays(): array;

    public function setMatchWeekdays(array $matchWeekdays): static;

    public function isActive(): bool;

    public function setActive(bool $active): static;

    public function getDiscriminator(): string;
}
