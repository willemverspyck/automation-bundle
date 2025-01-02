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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsCommand(name: 'spyck:automation:schedule', description: 'Command for schedule events.')]
final class ScheduleCommand extends Command
{
    public function __construct(private readonly EventDispatcherInterface $eventDispatcher, private readonly ScheduleRepository $scheduleRepository)
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = new DateTimeImmutable();

        $schedules = $this->scheduleRepository->getSchedules(ScheduleForSystem::class);

        foreach ($schedules as $schedule) {
            $scheduleEvent = new ScheduleEvent($schedule, $date);

            $this->eventDispatcher->dispatch($scheduleEvent);
        }

        return Command::SUCCESS;
    }
}
