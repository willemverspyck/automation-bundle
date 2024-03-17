<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
            ->autowire()
            ->autoconfigure();

    $services
        ->load('Spyck\\AutomationBundle\\', sprintf('%s/../src', __DIR__))
        ->exclude([
            sprintf('%s/../src/Entity/', __DIR__),
            sprintf('%s/../src/Kernel.php', __DIR__),
        ]);
};
