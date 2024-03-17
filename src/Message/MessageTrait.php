<?php

namespace Spyck\AutomationBundle\Message;

use Spyck\AutomationBundle\Entity\ModuleInterface;
use Spyck\AutomationBundle\Parameter\ParameterInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait MessageTrait
{
    private MessageBusInterface $messageBus;

    #[Required]
    public function setMessageBus(MessageBusInterface $messageBus): void
    {
        $this->messageBus = $messageBus;
    }

    public function putAutomationMessage(ModuleInterface $module, ParameterInterface $parameter, array $stamps = []): void
    {
        $moduleMessage = new ModuleMessage();
        $moduleMessage->setModule($module);
        $moduleMessage->setParameter($parameter);

        $this->messageBus->dispatch($moduleMessage, $stamps);
    }
}
