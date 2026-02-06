<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Command;

use Exception;
use RuntimeException;
use Spyck\AutomationBundle\Event\TaskEvent;
use Spyck\AutomationBundle\Repository\ModuleRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsCommand(name: 'spyck:automation:task', description: 'Execute task for module')]
final class TaskCommand extends Command
{
    public function __construct(private readonly EventDispatcherInterface $eventDispatcher, private readonly ModuleRepository $moduleRepository)
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

    /**
     * @throws Exception
     */
    public function __invoke(SymfonyStyle $style, #[Option(name: 'moduleId')] ?int $moduleId = null, #[Option(name: 'variableKey')] array $variableKeys = [], #[Option(name: 'variableValue')] array $variableValues = []): int
    {
        $module = $this->moduleRepository->getModuleById($moduleId);

        if (null === $module) {
            $style->error(sprintf('Module "%d" not found.', $moduleId));

            return Command::FAILURE;
        }

        if (count($variableKeys) !== count($variableValues)) {
            $style->error('Parameter "variableKey" and "variableValue" must be equal.');

            return Command::FAILURE;
        }

        $variables = array_combine($variableKeys, $variableValues);

        $style->info('Execute task');

        $taskEvent = new TaskEvent($module, $variables);

        $this->eventDispatcher->dispatch($taskEvent);

        $style->info('Done');

        return Command::SUCCESS;
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
