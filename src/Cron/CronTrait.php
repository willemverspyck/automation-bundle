<?php

namespace Spyck\AutomationBundle\Cron;

use App\Entity\Module;
use Exception;
use Spyck\AutomationBundle\Entity\Cron;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Parameter\ParameterInterface;
use Spyck\AutomationBundle\Repository\CronRepository;
use Symfony\Contracts\Service\Attribute\Required;

trait CronTrait
{
    private Cron $cron;
    private CronRepository $cronRepository;

    #[Required]
    public function setCronRepository(CronRepository $cronRepository): void
    {
        $this->cronRepository = $cronRepository;
    }

    public function getAutomationCron(): Cron
    {
        return $this->cron;
    }

    public function setAutomationCron(Cron $cron): void
    {
        $this->cron = $cron;
    }

    /**
     * @throws Exception
     */
    protected function putAutomationCron(ModuleInterface $module, ParameterInterface $parameters, int $priority = 1): void
    {
        $parent = null;

        $callbacks = $this->getAutomationCronCallbacks();

        $parameters = $parameters->getData();

        foreach ($callbacks as $callback) {
            $parent = $this->cronRepository->putCron($parent, $module, $callback, $parameters, $priority);
        }
    }
}
