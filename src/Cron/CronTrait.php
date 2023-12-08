<?php

namespace Spyck\AutomationBundle\Cron;

use App\Entity\Module;
use Exception;
use Spyck\AutomationBundle\Entity\Cron;
use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Parameter\ParameterListInterface;
use Spyck\AutomationBundle\Repository\CronRepository;
use Symfony\Contracts\Service\Attribute\Required;

trait CronTrait
{
    private CronRepository $cronRepository;
    private Cron $cron;

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
    protected function putAutomationCron(ModuleInterface $module, ParameterListInterface $parameters, int $priority = 1): void
    {
        $parent = null;

        $callbacks = $this->getAutomationCronCallbacks();

        $parameters = $parameters->getData();

        foreach ($callbacks as $callback) {
            $parent = $this->cronRepository->putCron($parent, $module, $callback, $parameters, $priority);
        }
    }
}
