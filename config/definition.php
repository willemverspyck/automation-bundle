<?php

declare(strict_types=1);

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition) {
    $definition->rootNode()
        ->children()
            ->arrayNode('cron')
                ->children()
                    ->integerNode('retry')
                        ->defaultValue(10)
                    ->end()
                    ->integerNode('timeout')
                        ->defaultValue(86400)
                    ->end()
                ->end()
                ->addDefaultsIfNotSet()
            ->end()
        ->end()
        ->children()
            ->arrayNode('module')
                ->children()
                    ->scalarNode('class')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
                ->isRequired()
            ->end()
        ->end();
};