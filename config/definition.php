<?php

declare(strict_types=1);

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition) {
    $definition->rootNode()
        ->children()
            ->arrayNode('cron')
                ->children()
                    ->arrayNode('retry')
                        ->children()
                            ->integerNode('delay')
                                ->defaultValue(3600)
                                ->min(0)
                            ->end()
                            ->integerNode('multiplier')
                                ->defaultValue(1)
                                ->min(0)
                            ->end()
                            ->integerNode('max')
                                ->defaultValue(24)
                                ->min(0)
                            ->end()
                        ->end()
                        ->addDefaultsIfNotSet()
                    ->end()
                    ->integerNode('timeout')
                        ->defaultValue(86400)
                        ->min(0)
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
