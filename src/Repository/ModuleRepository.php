<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry, #[Autowire(param: 'spyck.automation.config.module.class')] private readonly string $class)
    {
        parent::__construct($managerRegistry, $this->class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getModuleById(int $id): ?ModuleInterface
    {
        return $this->createQueryBuilder('module')
            ->where('module.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getModuleDataAsQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('module');
    }

    /**
     * @return array<int, ModuleInterface>
     */
    public function getModuleData(): array
    {
        return $this->getModuleDataAsQueryBuilder()
            ->getQuery()
            ->getResult();
    }
}
