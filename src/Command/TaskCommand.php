<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Command;

use DateTime;
use Exception;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Service\TaskService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'spyck:automation:task', description: 'Execute task for module')]
final class TaskCommand extends AbstractModuleCommand
{
    public function __construct(private readonly TaskService $taskService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'The "id" of the module?')
            ->addOption('date', null, InputOption::VALUE_REQUIRED, 'Parameter "date"', 'now')
            ->addOption('variableKey', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Variable key')
            ->addOption('variableValue', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Variable value');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $optionId = (int) $input->getOption('id');

        $optionVariableKey = $input->getOption('variableKey');
        $optionVariableValue = $input->getOption('variableValue');

        if (count($optionVariableKey) !== count($optionVariableValue)) {
            $style->error('Parameter "variableKey" and "variableValue" must be equal.');

            return Command::FAILURE;
        }

        $variables = array_combine($optionVariableKey, $optionVariableValue);

        $output->writeln('Execute task');

        $this->taskService->executeTaskByModuleId($optionId, $variables);

        $output->writeln('Done');

        return Command::SUCCESS;
    }

    protected function getModule(ModuleInterface $module): bool
    {
        return true;
    }
}
