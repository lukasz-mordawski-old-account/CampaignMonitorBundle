<?php

namespace LukaszMordawski\CampaignMonitorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package LukaszMordawski\CampaignMonitorBundle\DependencyInjection
 * @author Lukasz Mordawski <lukasz.mordawski@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     * @author Lukasz Mordawski <lukasz.mordawski@gmail.com>
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('lukasz_mordawski_campaign_monitor');

        $root
            ->children()
                ->scalarNode('api_key')
                    ->isRequired()
                ->end()
                ->scalarNode('client_id')
                    ->isRequired()
                ->end()
                ->scalarNode('cache_service_id')
                    ->defaultValue('campaign_monitor.cache')
                ->end()
                ->scalarNode('cache_lifetime')
                    ->defaultValue(3600)
                ->end()
            ->end();

        return $treeBuilder;
    }
}