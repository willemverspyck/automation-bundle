<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Event\Subscriber;

use DateTime;
use DateTimeInterface;
use Spyck\AutomationBundle\Entity\ScheduleInterface;
use Spyck\AutomationBundle\Event\ScheduleEvent;
use Spyck\AutomationBundle\Service\TaskService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ScheduleEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TaskService $taskService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScheduleEvent::class => [
                'onSchedule',
            ],
        ];
    }

    public function onSchedule(ScheduleEvent $event): void
    {
        $schedule = $event->getSchedule();
        $date = $event->getDate();

        if (false === $this->isMatchBySchedule($schedule, $date)) {
            return;
        }

        $this->taskService->executeTaskAsMessageBySchedule($schedule);
    }

    private function isMatch(array $data, array $matches): bool
    {
        if (0 === count($data)) {
            return true;
        }

        foreach ($matches as $match) {
            if (in_array($match, $data, true)) {
                return true;
            }
        }

        return false;
    }

    private function isMatchBySchedule(ScheduleInterface $schedule, DateTimeInterface $date): bool
    {
        if (false === $this->isMatch($schedule->getMatchHours(), $this->getMatch($date, 'G'))) {
            return false;
        }

        if (false === $this->isMatch($schedule->getMatchDays(), $this->getMatch($date, 'j'))) {
            return false;
        }

        if (false === $this->isMatch($schedule->getMatchWeeks(), $this->getMatch($date, 'W'))) {
            return false;
        }

        if (false === $this->isMatch($schedule->getMatchWeekdays(), $this->getMatch($date, 'N'))) {
            return false;
        }

        return true;
    }

    private function getMatch(DateTimeInterface $date, string $format): array
    {
        $matches = [
            (int) $date->format($format),
        ];

        if ('j' === $format) {
            $dateLastDay = DateTime::createFromInterface($date);
            $dateLastDay->modify('Last day of this month');

            if ($date->format('j') === $dateLastDay->format('j')) {
                $matches[] = 'L';
            }
        }

        return $matches;
    }
}
