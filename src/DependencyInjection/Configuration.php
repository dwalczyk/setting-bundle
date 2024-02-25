<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @internal
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('dwalczyk_setting');

        /* @phpstan-ignore-next-line */
        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('data_storage')->end()
            ->scalarNode('cache')->end()
            ->scalarNode('cache_lifetime')->defaultValue(3600 * 24 * 30)->end()
            ->arrayNode('definitions')
            ->arrayPrototype()
            ->children()
            ->scalarNode('name')->cannotBeEmpty()->end()
            ->scalarNode('type')->cannotBeEmpty()->defaultValue('string')->end()
            ->variableNode('default_value')->defaultNull()->end()
            ->arrayNode('options')->end()
            ->end()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
