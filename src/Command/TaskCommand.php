<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Command;

use Exception;
use RuntimeException;
use Spyck\AutomationBundle\Repository\ModuleRepository;
use Spyck\AutomationBundle\Service\TaskService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'spyck:automation:task', description: 'Execute task for module')]
final class TaskCommand extends Command
{
    public function __construct(private readonly ModuleRepository $moduleRepository, private readonly TaskService $taskService)
    {
        parent::__construct();
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestOptionValuesFor('id')) {
            $data = $this->getModules();

            $suggestions->suggestValues(array_keys($data));
        }
    }

    protected function configure(): void
    {
        $this
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'The "id" of the module?')
            ->addOption('date', null, InputOption::VALUE_REQUIRED, 'Parameter "date"', 'now')
            ->addOption('variableKey', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Variable key')
            ->addOption('variableValue', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Variable value');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $id = $input->getOption('id');

        if (null !== $id) {
            return;
        }

        $data = $this->getModules();

        $question = new ChoiceQuestion('Please select a module:', $data);
        $question->setMaxAttempts(2);
        $question->setValidator(function (string $answer): string {
            if (0 === preg_match('/^\d+$/', $answer)) {
                throw new RuntimeException('Unknown module');
            }

            return $answer;
        });

        $answer = $this->getHelper('question')->ask($input, $output, $question);

        $input->setOption('id', $answer);
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

    private function getModules(): array
    {
        $data = [];

        $modules = $this->moduleRepository->getModuleData();

        foreach ($modules as $module) {
            $data[$module->getId()] = $module->getAdapter();
        }

        return $data;
    }
}
