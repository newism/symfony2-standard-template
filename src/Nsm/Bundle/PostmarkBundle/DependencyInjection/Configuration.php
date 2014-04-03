<?php

namespace Nsm\Bundle\PostmarkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('nsm_postmark');

        $rootNode
            ->children()
                ->scalarNode('model_manager_name')
                ->defaultNull()
            ->end()
        ;

        $this->addPostmarkGroup($rootNode);

        return $treeBuilder;
    }

    private function addPostmarkGroup(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->integerNode('max_retries')
                    ->min(1)
                    ->max(10)
                    ->defaultValue(3)
                ->end()
                ->scalarNode('api_key')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('inbound_reply_address')->isRequired()->cannotBeEmpty()->end()
            ->end();
    }

    private function addSpoolItemGroup(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('spool_item')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('spool_item_class')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('spool_item_manager')
                            ->defaultValue('nsm_postmark.spool_item_manager.default')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addLogItemGroup(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('log_item')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('log_item_class')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('log_item_manager')
                            ->defaultValue('nsm_postmark.log_item_manager.default')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
