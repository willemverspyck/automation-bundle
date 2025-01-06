<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Doctrine;
use Spyck\AutomationBundle\Repository\TaskRepository;
use Stringable;

#[Doctrine\Entity(repositoryClass: TaskRepository::class)]
#[Doctrine\Table(name: 'automation_task')]
class Task implements Stringable, TimestampInterface
{
    use TimestampTrait;

    #[Doctrine\Column(name: 'id', type: Types::INTEGER)]
    #[Doctrine\Id]
    #[Doctrine\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[Doctrine\ManyToOne(targetEntity: ModuleInterface::class)]
    #[Doctrine\JoinColumn(name: 'module_id', referencedColumnName: 'id', nullable: false)]
    private ModuleInterface $module;

    #[Doctrine\Column(name: 'name', type: Types::STRING, length: 128)]
    private string $name;

    #[Doctrine\Column(name: 'variables', type: Types::JSON)]
    private array $variables;

    #[Doctrine\Column(name: 'priority', type: Types::SMALLINT, options: ['unsigned' => true])]
    private int $priority;

    #[Doctrine\Column(name: 'active', type: Types::BOOLEAN)]
    private bool $active;

    /**
     * @var Collection<int, ScheduleInterface>
     */
    #[Doctrine\ManyToMany(targetEntity: AbstractSchedule::class)]
    #[Doctrine\JoinTable(name: 'automation_task_schedule')]
    #[Doctrine\JoinColumn(name: 'task_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Doctrine\InverseJoinColumn(name: 'schedule_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $schedules;

    public function __construct()
    {
        $this->schedules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModule(): ModuleInterface
    {
        return $this->module;
    }

    public function setModule(ModuleInterface $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): self
    {
        $this->variables = $variables;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function addSchedule(ScheduleInterface $schedule): static
    {
        $this->schedules->add($schedule);

        return $this;
    }

    public function clearSchedules(): void
    {
        $this->schedules->clear();
    }

    /**
     * @return Collection<int, ScheduleInterface>
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function removeSchedule(ScheduleInterface $schedule): void
    {
        $this->schedules->removeElement($schedule);
    }

    public function __clone()
    {
        $this->id = null;

        $this->setName(sprintf('%s (Copy)', $this->getName()));
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
