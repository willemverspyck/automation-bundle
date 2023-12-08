<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Command;

use Spyck\AutomationBundle\Service\CronService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'spyck:automation:cron', description: 'Cron executor')]
final class CronCommand extends Command
{
    use LockableTrait;

    public function __construct(private readonly CronService $cronService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (false === $this->lock()) {
            $output->writeln('The command is already running in another process');

            return Command::SUCCESS;
        }

        $output->writeln('Looking for tasks to execute...');

        $this->cronService->executeCron();

        $output->writeln('Done');

        $this->release();

        return Command::SUCCESS;
    }
}
