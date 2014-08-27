<?php

namespace Stevens\MainBundle\Tests\Service\CampaignMonitor;

use LukaszMordawski\CampaignMonitorBundle\Helper\FactoryArguments;
use LukaszMordawski\CampaignMonitorBundle\Service\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase {

    public function testCanConstruct() {
        $factory = new Factory('2321312');
        $this->assertInstanceOf('LukaszMordawski\CampaignMonitorBundle\Service\Factory', $factory);
    }

    public function testFactory_WithoutAdditionalArgument() {
        $factory = new Factory('2321312');
        $args = new FactoryArguments();
        $args->endpoint = 'general';
        $service = $factory->factory($args);

        $this->assertInstanceOf('CS_REST_General', $service);
    }

    public function factoryWithSecondParameterProvider() {
        return [
            [ 'campaigns', 'campaignId', 'CS_REST_Campaigns', '_campaigns_base_route' ],
            [ 'clients', 'clientId', 'CS_REST_Clients', '_clients_base_route' ],
            [ 'lists', 'listId', 'CS_REST_Lists', '_lists_base_route' ],
            [ 'segments', 'segmentId', 'CS_REST_Segments', '_segments_base_route' ]
        ];
    }

    /**
     * @param $endpoint
     * @param $expectedClassName
     * @dataProvider factoryWithSecondParameterProvider
     */
    public function testFactory_WithSecondParameter($endpoint, $secondArg, $expectedClassName, $propertyToCheck) {
        $factory = new Factory('2321312');

        $args = new FactoryArguments();
        $args->endpoint = $endpoint;
        $args->$secondArg = '123456';

        $service = $factory->factory($args);

        $this->assertInstanceOf($expectedClassName, $service);
        $reflection = new \ReflectionClass($expectedClassName);
        $prop = $reflection->getProperty($propertyToCheck);
        $prop->setAccessible(true);
        $value = $prop->getValue($service);

        $this->assertRegExp('#/123456/?$#', $value);
    }



}