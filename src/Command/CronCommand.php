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

    public function __invoke(SymfonyStyle $style): int
    {
        if (false === $this->lock()) {
            $style->error('The command is already running in another process');

            return Command::SUCCESS;
        }

        $style->info('Looking for crons to execute...');

        $this->cronService->executeCron();

        $this->release();

        $style->success('Done');

        return Command::SUCCESS;
    }
}
