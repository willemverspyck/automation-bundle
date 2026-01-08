<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Command;

use DateTimeImmutable;
use Exception;
use Spyck\AutomationBundle\Entity\ScheduleForSystem;
use Spyck\AutomationBundle\Event\ScheduleEvent;
use Spyck\AutomationBundle\Repository\ScheduleRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsCommand(name: 'spyck:automation:schedule', description: 'Command for schedule events.')]
final class ScheduleCommand
{
    public function __construct(private readonly EventDispatcherInterface $eventDispatcher, private readonly ScheduleRepository $scheduleRepository)
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(SymfonyStyle $style): int
    {
        $style->info('Looking for schedules to execute...');

        $date = new DateTimeImmutable();

        $schedules = $this->scheduleRepository->getSchedules(ScheduleForSystem::class);

        foreach ($schedules as $schedule) {
            $scheduleEvent = new ScheduleEvent($schedule, $date);

            $this->eventDispatcher->dispatch($scheduleEvent);
        }

        $style->success('Done');

        return Command::SUCCESS;
    }
}
