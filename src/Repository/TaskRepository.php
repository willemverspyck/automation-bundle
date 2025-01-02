<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Spyck\AutomationBundle\Entity\ScheduleInterface;
use Spyck\AutomationBundle\Entity\Task;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Task::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getTaskById(int $id): ?Task
    {
        return $this->createQueryBuilder('task')
            ->where('task.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<int, Task>
     */
    public function getTasksBySchedule(ScheduleInterface $schedule): array
    {
        return $this->createQueryBuilder('task')
            ->innerJoin('task.schedule', 'schedule', Join::WITH, 'schedule = :schedule')
            ->where('task.active = TRUE')
            ->orderBy('task.priority')
            ->setParameter('schedule', $schedule)
            ->getQuery()
            ->getResult();
    }
}
