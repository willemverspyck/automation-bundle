<?php

declare(strict_types=1);

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition) {
    $definition->rootNode()
        ->children()
            ->arrayNode('user')
                ->children()
                    ->scalarNode('class')->isRequired()->end()
                ->end()
            ->end()
        ->end();
};