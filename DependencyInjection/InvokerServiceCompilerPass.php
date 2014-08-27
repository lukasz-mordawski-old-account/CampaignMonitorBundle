<?php

namespace LukaszMordawski\CampaignMonitorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class InvokerServiceCompilerPass
 * @package LukaszMordawski\CampaignMonitorBundle\DependencyInjection
 * @author Lukasz Mordawski <lukasz.mordawski@gmail.com>
 *
 * This compiler pass sets proper cache service as parameter of Invoker service
 */
class InvokerServiceCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('campaign_monitor.invoker');
        $arguments = $definition->getArguments();
        $arguments[1] = new Reference($container->getParameter('lukaszmordawski_create_send_cache_service_id'));

        $definition->setArguments($arguments);
    }
}