<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Service;

use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Psr\Log\LoggerInterface;
use Spyck\AutomationBundle\Cron\CronInterface;
use Spyck\AutomationBundle\Entity\Cron;
use Spyck\AutomationBundle\Event\PostCronEvent;
use Spyck\AutomationBundle\Event\PreCronEvent;
use Spyck\AutomationBundle\Exception\RetryException;
use Spyck\AutomationBundle\Repository\CronRepository;
use Spyck\AutomationBundle\Utility\DateTimeUtility;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Throwable;

readonly class CronService
{
    public function __construct(private CronRepository $cronRepository, private EventDispatcherInterface $eventDispatcher, private JobService $jobService, private LoggerInterface $logger, private MapService $mapService, #[Autowire(param: 'spyck.automation.config.cron.retry.delay')] private int $retryDelay, #[Autowire(param: 'spyck.automation.config.cron.retry.multiplier')] private int $retryMultiplier, #[Autowire(param: 'spyck.automation.config.cron.retry.max')] private int $retryMax, #[Autowire(param: 'spyck.automation.config.cron.timeout')] private int $timeout)
    {
    }

    /**
     * @throws Exception
     * @throws NonUniqueResultException
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

        $timestamp = new DateTimeImmutable();

        $this->cronRepository->patchCron(cron: $cron, fields: ['status', 'duration', 'messages', 'timestamp'], status: Cron::STATUS_PENDING, timestamp: $timestamp);

        $fields = ['status', 'duration'];

        $status = null;
        $messages = null;
        $errors = null;
        $timestampAvailable = null;

        $job = $this->jobService->getJobByModule($cron->getModule());

        try {
            if (false === $job instanceof CronInterface) {
                throw new Exception(sprintf('"%s" is no instance of CronInterface', get_class($job)));
            }

            $map = $this->mapService->getMap($cron->getVariables(), $job->getAutomationCronParameter());

            $preCronEvent = new PreCronEvent($job, $map);

            $this->eventDispatcher->dispatch($preCronEvent);

            $job->setAutomationCron($cron);
            $job->executeAutomationCron($cron->getCallback(), $map);

            $fields = array_merge($fields, ['errors', 'timestampAvailable']);

            $cron = $job->getAutomationCron();

            $duration = $this->getDuration($cron->getTimestamp());

            $this->cronRepository->patchCron(cron: $cron, fields: $fields, status: Cron::STATUS_COMPLETE, duration: $duration, messages: $messages, errors: $errors, timestampAvailable: $timestampAvailable);

            $postCronEvent = new PostCronEvent($job, $map);

            $this->eventDispatcher->dispatch($postCronEvent);
        } catch (RetryException $exception) {
            $fields = array_merge($fields, ['messages', 'errors', 'timestampAvailable']);

            $messages = $this->getMessages($cron->getMessages(), $exception);

            $errors = null === $cron->getErrors() ? 1 : $cron->getErrors() + 1;

            if ($errors >= $this->retryMax) {
                $status = Cron::STATUS_ERROR;

                $this->logger->error('Cron failed', [
                    'module' => (string) $cron->getModule(),
                    'callback' => $cron->getCallback(),
                    'variables' => $cron->getVariables(),
                ]);
            } else {
                $timestampAvailable = new DateTimeImmutable(sprintf('%d seconds', pow($errors, $this->retryMultiplier) * $this->retryDelay));
            }

            $cron = $job->getAutomationCron();

            $duration = $this->getDuration($cron->getTimestamp());

            $this->cronRepository->patchCron(cron: $cron, fields: $fields, status: $status, duration: $duration, messages: $messages, errors: $errors, timestampAvailable: $timestampAvailable);
        } catch (Throwable $throwable) {
            $fields = array_merge($fields, ['messages']);

            $status = Cron::STATUS_ERROR;
            $messages = $this->getMessages($cron->getMessages(), $throwable);

            $this->logger->error('Cron failed', [
                'module' => (string) $cron->getModule(),
                'callback' => $cron->getCallback(),
                'variables' => $cron->getVariables(),
            ]);

            $cron = $job->getAutomationCron();

            $duration = $this->getDuration($cron->getTimestamp());

            $this->cronRepository->patchCron(cron: $cron, fields: $fields, status: $status, duration: $duration, messages: $messages, errors: $errors, timestampAvailable: $timestampAvailable);
        }
    }

    public function resetCron(Cron $cron, bool $check = false): void
    {
        if (false === $check || Cron::STATUS_ERROR === $cron->getStatus()) {
            $this->cronRepository->patchCron(cron: $cron, fields: ['status', 'duration', 'messages', 'errors', 'timestamp', 'timestampAvailable']);

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
            $date = new DateTimeImmutable();
            $timestamp = $cron->getTimestamp();

            if ($date->getTimestamp() - $timestamp->getTimestamp() > $this->timeout) {
                $duration = $this->getDuration($timestamp);

                $messages = $cron->getMessages() ?? [];
                $messages[] = sprintf('Timeout after %s', DateTimeUtility::getDurationAsText($timestamp, $date));

                $this->cronRepository->patchCron(cron: $cron, fields: ['status', 'duration', 'messages'], status: Cron::STATUS_ERROR, duration: $duration, messages: $messages);
            }
        }
    }

    private function getDuration(DateTimeImmutable $dateTimeStart): int
    {
        $dateTimeEnd = new DateTimeImmutable();

        return $dateTimeEnd->getTimestamp() - $dateTimeStart->getTimestamp();
    }

    private function getMessages(?array $messages, Throwable $throwable): array
    {
        return [
            ...$messages ?? [],
            sprintf('%s (%s: %s)', $throwable->getMessage(), $throwable->getFile(), $throwable->getLine()),
            ...explode(PHP_EOL, $throwable->getTraceAsString()),
        ];
    }
}
