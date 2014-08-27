<?php

namespace LukaszMordawski\CampaignMonitorBundle;

use LukaszMordawski\CampaignMonitorBundle\DependencyInjection\InvokerServiceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class LukaszMordawskiCampaignMonitorBundle
 * @package LukaszMordawski\CampaignMonitorBundle
 */
class LukaszMordawskiCampaignMonitorBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new InvokerServiceCompilerPass());
    }
}
