<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Repository;

use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Spyck\AutomationBundle\Entity\Cron;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Utility\DataUtility;

class CronRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Cron::class);
    }

    /**
     * Get cron by id.
     */
    public function getCronById(int $id): ?Cron
    {
        return $this->findOneBy([
            'id' => $id,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getCron(): ?Cron
    {
        $timestampAvailable = new DateTime();

        return $this->createQueryBuilder('cron')
            ->addSelect('cronParent')
            ->leftJoin('cron.parent', 'cronParent')
            ->where('cron.status IS NULL')
            ->andWhere('cron.timestampAvailable IS NULL OR cron.timestampAvailable <= :timestampAvailable')
            ->having('cronParent IS NULL')
            ->orHaving('cronParent.status = :status')
            ->orderBy('cron.priority')
            ->addOrderBy('cron.id')
            ->setMaxResults(1)
            ->setParameter('status', Cron::STATUS_COMPLETE)
            ->setParameter('timestampAvailable', $timestampAvailable)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get cron data by status.
     *
     * @return array<int, Cron>
     */
    public function getCronDataByStatus(string $status): array
    {
        return $this->findBy([
            'status' => $status,
        ]);
    }

    public function putCron(?Cron $parent, ModuleInterface $module, string $callback, array $variables, int $priority = 1): Cron
    {
        $cron = new Cron();
        $cron->setParent($parent);
        $cron->setModule($module);
        $cron->setCallback($callback);
        $cron->setVariables($variables);
        $cron->setPriority($priority);

        $this->getEntityManager()->persist($cron);
        $this->getEntityManager()->flush();

        return $cron;
    }

    public function patchCron(Cron $cron, array $fields, Cron $parent = null, ModuleInterface $module = null, string $callback = null, array $variables = null, int $priority = null, string $status = null, int $duration = null, array $messages = null, int $errors = null, DateTimeInterface $timestamp = null, DateTimeInterface $timestampAvailable = null): void
    {
        if (in_array('parent', $fields)) {
            $cron->setParent($parent);
        }

        if (in_array('module', $fields)) {
            DataUtility::assert(null !== $module);

            $cron->setModule($module);
        }

        if (in_array('callback', $fields)) {
            DataUtility::assert(null !== $callback);

            $cron->setCallback($callback);
        }

        if (in_array('variables', $fields)) {
            DataUtility::assert(null !== $variables);

            $cron->setVariables($variables);
        }

        if (in_array('priority', $fields)) {
            DataUtility::assert(null !== $priority);

            $cron->setPriority($priority);
        }

        if (in_array('status', $fields)) {
            $cron->setStatus($status);
        }

        if (in_array('duration', $fields)) {
            $cron->setDuration($duration);
        }

        if (in_array('messages', $fields)) {
            $cron->setMessages($messages);
        }

        if (in_array('errors', $fields)) {
            $cron->setErrors($errors);
        }

        if (in_array('timestamp', $fields)) {
            $cron->setTimestamp($timestamp);
        }

        if (in_array('timestampAvailable', $fields)) {
            $cron->setTimestampAvailable($timestampAvailable);
        }

        $this->getEntityManager()->persist($cron);
        $this->getEntityManager()->flush();
    }
}
