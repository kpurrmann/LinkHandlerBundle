<?php

declare(strict_types=1);

namespace PurrmannWebsolutions\LinkHandlerBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    const CONFIGURATION_ROOT = 'pw_linkhander';

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(self::CONFIGURATION_ROOT);
        $treeBuilder->getRootNode()
                ->children()
                    ->arrayNode('entities')
                        ->canBeUnset(false)
                        ->prototype('array')
                            ->useAttributeAsKey('method')
                            ->prototype('array')
                            ->children()
                                ->scalarNode('route')
                                    ->validate()
                                        ->ifTrue(function($v) {
                                            return !is_string($v);
                                        })
                                        ->thenInvalid('route cant be empty')
                                    ->end()
                                ->end()
                                ->arrayNode('methodParameters')
                                    ->useAttributeAsKey('name')
                                        ->prototype('scalar')
                                    ->end()
                                ->end()
                                ->arrayNode('parameters')
                                    ->useAttributeAsKey('name')
                                        ->prototype('scalar')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
