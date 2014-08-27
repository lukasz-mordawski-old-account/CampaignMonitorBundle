<?php

namespace LukaszMordawski\CampaignMonitorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class LukaszMordawskiCampaignMonitorExtension
 * @package LukaszMordawski\CampaignMonitorBundle\DependencyInjection
 * @author Lukasz Mordawski <lukasz.mordawski@gmail.com>
 */
class LukaszMordawskiCampaignMonitorExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('lukaszmordawski_create_send_api_key', $config['api_key']);
        $container->setParameter('lukaszmordawski_create_send_client_id', $config['client_id']);
        $container->setParameter('lukaszmordawski_create_send_cache_service_id', $config['cache_service_id']);
        $container->setParameter('lukaszmordawski_create_send_cache_lifetime', $config['cache_lifetime']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
