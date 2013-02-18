<?php

namespace Undf\AngularJsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @author Dani Gonzalez <daniel.gonzalez@undefined.es>
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder
                ->root('undf_angular_js')
                    ->fixXmlConfig('config')
                    ->children()
                        ->arrayNode('module_sets')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->useAttributeAsKey('name')
                                ->prototype('variable')->end()
                            ->end()
                        ->end()
                        ->arrayNode('catalogue')
                            ->useAttributeAsKey('parent')
                            ->prototype('array')
                                ->useAttributeAsKey('alias')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('description')->end()
                                        ->arrayNode('files')
                                            ->requiresAtLeastOneElement()
                                            ->prototype('scalar')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
        ;
        return $treeBuilder;
    }

}
