<?php

namespace Spyck\AutomationBundle\Message;

use Exception;
use Spyck\AutomationBundle\Job\JobInterface;
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

    public function putAutomationMessage(ParameterInterface $parameter, array $stamps = []): void
    {
        $moduleMessage = new ModuleMessage();
        $moduleMessage->setModule($this->getAutomationModule());
        $moduleMessage->setParameter($parameter);

        $this->messageBus->dispatch($moduleMessage, $stamps);
    }
}
