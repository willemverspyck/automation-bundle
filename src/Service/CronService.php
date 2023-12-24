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
    public function __construct(private readonly CronRepository $cronRepository, private readonly JobService $jobService, private readonly LoggerInterface $logger, private readonly MapService $mapService, #[Autowire(param: 'spyck.automation.cron.retry')] private readonly int $retry, #[Autowire(param: 'spyck.automation.cron.timeout')] private readonly int $timeout)
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

        $this->cronRepository->patchCron(cron: $cron, fields: ['status', 'duration', 'log', 'timestamp'], status: Cron::STATUS_PENDING, timestamp: $timestamp);

        $fields = ['status', 'duration'];

        $status = null;
        $log = null;
        $error = null;
        $timestampAvailable = null;

        $job = $this->jobService->getJobByModule($cron->getModule());

        try {
            if ($job instanceof CronInterface) {
                $map = $this->mapService->getMap($cron->getParameters(), $job->getAutomationCronParameter());

                $job->setAutomationCron($cron);
                $job->executeAutomationCron($cron->getCallback(), $map);
            } else {
                throw new Exception(sprintf('"%s" is no instance of CronInterface', get_class($job)));
            }

            $fields = array_merge($fields, ['error', 'timestampAvailable']);

            $status = Cron::STATUS_COMPLETE;
        } catch (RetryException $exception) {
            $fields = array_merge($fields, ['log', 'error', 'timestampAvailable']);

            $log = $this->getLog($exception, $cron->getLog());

            $error = null === $cron->getError() ? 1 : $cron->getError() + 1;

            if ($error >= $this->retry) {
                $status = Cron::STATUS_ERROR;

                $this->logger->error('Cron failed', [
                    'module' => (string) $cron->getModule(),
                    'callback' => $cron->getCallback(),
                    'parameters' => $cron->getParameters(),
                ]);
            } else {
                $timestampAvailable = new DateTime(sprintf('%d minutes', 15 * pow(2, $error - 1)));
            }
        } catch (Throwable $throwable) {
            $fields = array_merge($fields, ['log']);

            $status = Cron::STATUS_ERROR;
            $log = $this->getLog($throwable, $cron->getLog());

            $this->logger->error('Cron failed', [
                'module' => (string) $cron->getModule(),
                'callback' => $cron->getCallback(),
                'parameters' => $cron->getParameters(),
            ]);
        }

        $cron = $job->getAutomationCron();

        $duration = $this->getDuration($cron->getTimestamp());

        $this->cronRepository->patchCron($cron, $fields, null, null, null, null, null, $status, $duration, $log, $error, null, $timestampAvailable);
    }

    public function resetCron(Cron $cron, bool $check = false): void
    {
        if (false === $check || Cron::STATUS_ERROR === $cron->getStatus()) {
            $this->cronRepository->patchCron($cron, ['status', 'duration', 'log', 'timestamp']);

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

                $log = $cron->getLog();
                $log[] = sprintf('Timeout after %s', DateTimeUtility::getDurationAsText($timestamp, $date));

                $this->cronRepository->patchCron($cron, ['status', 'duration', 'log'], null, null, null, null, null, Cron::STATUS_ERROR, $duration, $log);
            }
        }
    }

    private function getDuration(DateTimeInterface $dateTimeStart): int
    {
        $dateTimeEnd = new DateTime();

        return $dateTimeEnd->getTimestamp() - $dateTimeStart->getTimestamp();
    }

    private function getLog(Throwable $throwable): array
    {
        return [
            sprintf('%s (%s: %s)', $throwable->getMessage(), $throwable->getFile(), $throwable->getLine()),
            ...explode(PHP_EOL, $throwable->getTraceAsString()),
        ];
    }
}
