<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Command;

use RuntimeException;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Polyfill\Intl\Icu\Exception\MethodNotImplementedException;

abstract class AbstractModuleCommand extends Command
{
    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestOptionValuesFor('id')) {
            $data = $this->getModules();

            $suggestions->suggestValues(array_keys($data));
        }
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

    protected function getModule(ModuleInterface $module): bool
    {
        throw new MethodNotImplementedException(__METHOD__);
    }

    private function getModules(): array
    {
        $data = [];

        $modules = $this->moduleRepository->getModuleData();

        foreach ($modules as $module) {
            $data[$module->getId()] = $module->getName();
        }

        return $data;
    }
}
