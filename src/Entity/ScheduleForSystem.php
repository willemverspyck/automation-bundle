<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Entity;

use Doctrine\ORM\Mapping as Doctrine;

#[Doctrine\Entity]
class ScheduleForSystem extends AbstractSchedule
{
    public function getDiscriminator(): string
    {
        return 'System';
    }
}
