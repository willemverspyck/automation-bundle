<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Command;

use Spyck\AutomationBundle\Service\CronService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'spyck:automation:cron', description: 'Cron executor')]
final class CronCommand
{
    use LockableTrait;

    public function __construct(private readonly CronService $cronService)
    {
    }

    public function __invoke(SymfonyStyle $symfonyStyle): int
    {
        if (false === $this->lock()) {
            $symfonyStyle->writeln('The command is already running in another process');

            return Command::SUCCESS;
        }

        $symfonyStyle->writeln('Looking for crons to execute...');

        $this->cronService->executeCron();

        $symfonyStyle->writeln('Done');

        $this->release();

        return Command::SUCCESS;
    }
}
