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
        if ($input->mustSuggestOptionValuesFor('moduleId')) {
            $data = $this->getModules();

            $suggestions->suggestValues(array_keys($data));
        }
    }

    protected function configure(): void
    {
        $this
            ->addOption('moduleId', null, InputOption::VALUE_REQUIRED, 'The "id" of the module?')
            ->addOption('variableKey', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Variable key')
            ->addOption('variableValue', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Variable value');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $moduleId = $input->getOption('moduleId');

        if (null !== $moduleId) {
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

        $input->setOption('moduleId', $answer);
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $moduleId = (int) $input->getOption('moduleId');
        $variableKeys = $input->getOption('variableKey');
        $variableValues = $input->getOption('variableValue');

        if (count($variableKeys) !== count($variableValues)) {
            $style->error('Parameter "variableKey" and "variableValue" must be equal.');

            return Command::FAILURE;
        }

        $variables = array_combine($variableKeys, $variableValues);

        $output->writeln('Execute task');

        $this->taskService->executeTaskAsMessage($moduleId, $variables);

        $output->writeln('Done');

        return Command::SUCCESS;
    }

    private function getModules(): array
    {
        $data = [];

        $modules = $this->moduleRepository->getModules();

        foreach ($modules as $module) {
            $data[$module->getId()] = $module->getAdapter();
        }

        return $data;
    }
}
