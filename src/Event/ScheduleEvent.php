<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Event;

use DateTimeInterface;
use Spyck\AutomationBundle\Entity\ScheduleInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class ScheduleEvent extends Event
{
    public function __construct(private readonly ScheduleInterface $schedule, private readonly DateTimeInterface $date)
    {
    }

    public function getSchedule(): ScheduleInterface
    {
        return $this->schedule;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
