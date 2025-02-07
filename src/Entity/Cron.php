<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Doctrine;
use Spyck\AutomationBundle\Repository\CronRepository;
use Stringable;

#[Doctrine\Entity(repositoryClass: CronRepository::class)]
#[Doctrine\Table(name: 'automation_cron')]
class Cron implements Stringable, TimestampInterface
{
    use TimestampTrait;

    public const string STATUS_COMPLETE = 'complete';
    public const string STATUS_COMPLETE_NAME = 'Complete';
    public const string STATUS_ERROR = 'error';
    public const string STATUS_ERROR_NAME = 'Error';
    public const string STATUS_PENDING = 'pending';
    public const string STATUS_PENDING_NAME = 'Pending';

    #[Doctrine\Column(name: 'id', type: Types::INTEGER)]
    #[Doctrine\Id]
    #[Doctrine\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[Doctrine\ManyToOne(targetEntity: Cron::class, inversedBy: 'children')]
    #[Doctrine\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true)]
    private ?Cron $parent = null;

    /**
     * @var Collection<int, Cron>
     */
    #[Doctrine\OneToMany(mappedBy: 'parent', targetEntity: Cron::class)]
    private Collection $children;

    #[Doctrine\ManyToOne(targetEntity: ModuleInterface::class)]
    #[Doctrine\JoinColumn(name: 'module_id', referencedColumnName: 'id', nullable: false)]
    private ModuleInterface $module;

    #[Doctrine\Column(name: 'callback', type: Types::STRING, length: 256)]
    private string $callback;

    #[Doctrine\Column(name: 'variables', type: Types::JSON)]
    private array $variables;

    #[Doctrine\Column(name: 'priority', type: Types::SMALLINT, options: ['unsigned' => true])]
    private int $priority;

    #[Doctrine\Column(name: 'status', type: Types::STRING, length: 16, nullable: true)]
    private ?string $status = null;

    #[Doctrine\Column(name: 'duration', type: Types::INTEGER, nullable: true)]
    private ?int $duration = null;

    #[Doctrine\Column(name: 'messages', type: Types::JSON, nullable: true)]
    private ?array $messages = null;

    #[Doctrine\Column(name: 'errors', type: Types::SMALLINT, nullable: true)]
    private ?int $errors = null;

    #[Doctrine\Column(name: 'timestamp', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $timestamp = null;

    #[Doctrine\Column(name: 'timestamp_available', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $timestampAvailable = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParent(): ?Cron
    {
        return $this->parent;
    }

    public function setParent(?Cron $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function addChild(Cron $child): self
    {
        $this->children->add($child);

        return $this;
    }

    public function clearChildren(): void
    {
        $this->children->clear();
    }

    /**
     * @return Collection<int, Cron>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function removeChild(Cron $child): void
    {
        $this->children->removeElement($child);
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

    public function getCallback(): string
    {
        return $this->callback;
    }

    public function setCallback(string $callback): self
    {
        $this->callback = $callback;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getMessages(): ?array
    {
        return $this->messages;
    }

    public function setMessages(?array $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    public function getErrors(): ?int
    {
        return $this->errors;
    }

    public function setErrors(?int $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function getTimestamp(): ?DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(?DateTimeImmutable $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getTimestampAvailable(): ?DateTimeImmutable
    {
        return $this->timestampAvailable;
    }

    public function setTimestampAvailable(?DateTimeImmutable $timestampAvailable): self
    {
        $this->timestampAvailable = $timestampAvailable;

        return $this;
    }

    public static function getStatusData(bool $inverse = false): array
    {
        $data = [
            self::STATUS_ERROR => self::STATUS_ERROR_NAME,
            self::STATUS_PENDING => self::STATUS_PENDING_NAME,
            self::STATUS_COMPLETE => self::STATUS_COMPLETE_NAME,
        ];

        if (false === $inverse) {
            return $data;
        }

        return array_flip($data);
    }

    public function __toString(): string
    {
        return sprintf('%s of %s', ucfirst($this->getCallback()), $this->getModule());
    }
}
