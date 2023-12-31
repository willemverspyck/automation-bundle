<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Service;

use Spyck\AutomationBundle\Cron\CronInterface;
use Spyck\AutomationBundle\Entity\Cron;
use Spyck\AutomationBundle\Exception\RetryException;
use DateTime;
use DateTimeInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Spyck\AutomationBundle\Repository\CronRepository;
use Spyck\AutomationBundle\Utility\DateTimeUtility;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ErrorHandler\Error\OutOfMemoryError;
use Throwable;

class CronService
{
    public function __construct(private readonly CronRepository $cronRepository, private readonly JobService $jobService, private readonly LoggerInterface $logger, private readonly MapService $mapService, #[Autowire(param: 'spyck.automation.cron.retry.delay')] private readonly int $retryDelay, #[Autowire(param: 'spyck.automation.cron.retry.multiplier')] private readonly int $retryMultiplier, #[Autowire(param: 'spyck.automation.cron.retry.max')] private readonly int $retryMax, #[Autowire(param: 'spyck.automation.cron.timeout')] private readonly int $timeout)
    {
    }

    /**
     * Execute the cron.
     */
    public function executeCron(): void
    {
        $crons = $this->cronRepository->getCronDataByStatus(Cron::STATUS_PENDING);

        if (count($crons) > 0) {
            $this->resetCronAfterTimeout($crons);

            return;
        }

        $cron = $this->cronRepository->getCron();

        if (null === $cron) {
            return;
        }

        $timestamp = new DateTime();

        $this->cronRepository->patchCron(cron: $cron, fields: ['status', 'duration', 'messages', 'timestamp'], status: Cron::STATUS_PENDING, timestamp: $timestamp);

        $fields = ['status', 'duration'];

        $status = null;
        $messages = null;
        $error = null;
        $timestampAvailable = null;

        $job = $this->jobService->getJobByModule($cron->getModule());

        try {
            if (false === $job instanceof CronInterface) {
                throw new Exception(sprintf('"%s" is no instance of CronInterface', get_class($job)));
            }

            $map = $this->mapService->getMap($cron->getVariables(), $job->getAutomationCronParameter());

            $job->setAutomationCron($cron);
            $job->executeAutomationCron($cron->getCallback(), $map);

            $fields = array_merge($fields, ['error', 'timestampAvailable']);

            $status = Cron::STATUS_COMPLETE;
        } catch (RetryException $exception) {
            $fields = array_merge($fields, ['messages', 'error', 'timestampAvailable']);

            $messages = $this->getMessages($exception, $cron->getMessages());

            $error = null === $cron->getError() ? 1 : $cron->getError() + 1;

            if ($error >= $this->retryMax) {
                $status = Cron::STATUS_ERROR;

                $this->logger->error('Cron failed', [
                    'module' => (string) $cron->getModule(),
                    'callback' => $cron->getCallback(),
                    'variables' => $cron->getVariables(),
                ]);
            } else {
                $timestampAvailable = new DateTime(sprintf('%d seconds', pow($error, $this->retryMultiplier) * $this->retryDelay));
            }
        } catch (Throwable $throwable) {
            $fields = array_merge($fields, ['messages']);

            $status = Cron::STATUS_ERROR;
            $messages = $this->getMessages($throwable, $cron->getMessages());

            $this->logger->error('Cron failed', [
                'module' => (string) $cron->getModule(),
                'callback' => $cron->getCallback(),
                'variables' => $cron->getVariables(),
            ]);
        }

        $cron = $job->getAutomationCron();

        $duration = $this->getDuration($cron->getTimestamp());

        $this->cronRepository->patchCron(cron: $cron, fields: $fields, status: $status, duration: $duration, messages: $messages, error: $error, timestampAvailable: $timestampAvailable);
    }

    public function resetCron(Cron $cron, bool $check = false): void
    {
        if (false === $check || Cron::STATUS_ERROR === $cron->getStatus()) {
            $this->cronRepository->patchCron(cron: $cron, fields: ['status', 'duration', 'messages', 'timestamp']);

            foreach ($cron->getChildren() as $child) {
                $this->resetCron($child);
            }
        }
    }

    /**
     * @param array<int, Cron> $crons
     */
    public function resetCronAfterTimeout(array $crons): void
    {
        foreach ($crons as $cron) {
            $date = new DateTime();
            $timestamp = $cron->getTimestamp();

            if ($date->getTimestamp() - $timestamp->getTimestamp() > $this->timeout) {
                $duration = $this->getDuration($timestamp);

                $messages = $cron->getMessages();
                $messages[] = sprintf('Timeout after %s', DateTimeUtility::getDurationAsText($timestamp, $date));

                $this->cronRepository->patchCron(cron: $cron, fields: ['status', 'duration', 'messages'], status: Cron::STATUS_ERROR, duration: $duration, messages: $messages);
            }
        }
    }

    private function getDuration(DateTimeInterface $dateTimeStart): int
    {
        $dateTimeEnd = new DateTime();

        return $dateTimeEnd->getTimestamp() - $dateTimeStart->getTimestamp();
    }

    private function getMessages(Throwable $throwable): array
    {
        return [
            sprintf('%s (%s: %s)', $throwable->getMessage(), $throwable->getFile(), $throwable->getLine()),
            ...explode(PHP_EOL, $throwable->getTraceAsString()),
        ];
    }
}
