<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class SpyckAutomationBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        $builder->setParameter('spyck.automation.config.cron.retry.delay', $config['cron']['retry']['delay']);
        $builder->setParameter('spyck.automation.config.cron.retry.multiplier', $config['cron']['retry']['multiplier']);
        $builder->setParameter('spyck.automation.config.cron.retry.max', $config['cron']['retry']['max']);
        $builder->setParameter('spyck.automation.config.cron.timeout', $config['cron']['timeout']);

        $builder->setParameter('spyck.automation.config.module.class', $config['module']['class']);
    }
}
