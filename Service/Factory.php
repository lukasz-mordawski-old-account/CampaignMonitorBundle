<?php

namespace LukaszMordawski\CampaignMonitorBundle\Service;

use Doctrine\Common\Inflector\Inflector;
use LukaszMordawski\CampaignMonitorBundle\Helper\FactoryArguments;

/**
 * Class Factory
 * @package LukaszMordawski\CampaignMonitorBundle\Service
 * @author Lukasz Mordawski <lukasz.mordawski@gmail.com>
 *
 * Factory class for CreateSend REST API wrappers
 */
class Factory
{
    /** @var String */
    private $apiKey;

    /**
     * @param String $apiKey
     * @author Lukasz Mordawski <lukasz.mordawski@gmail.com>
     *
     * Constructor
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param FactoryArguments $args
     * @return \CS_REST_Wrapper_Base
     * @author Lukasz Mordawski <lukasz.mordawski@gmail.com>
     *
     * This method returns proper CreateSend REST API wrapper
     */
    public function factory(FactoryArguments $args)
    {
        $className = $this->getClassName($args->endpoint);
        $reflection = new \ReflectionClass($className);

        $params = [['api_key' => $this->apiKey]];

        if ($this->isAdditionalParameterNeeded($reflection)) {
            $additionalParam = $this->getAdditionalParameter($reflection, $args);
            array_unshift($params, $additionalParam);
        }

        return $reflection->newInstanceArgs($params);
    }

    /**
     * @param \ReflectionClass $reflection
     * @param FactoryArguments $args
     * @return String|Integer
     * @author Lukasz Mordawski <lukasz.mordawski@gmail.com>
     *
     * This method gets additional parameter that must be passed to wrapper constructor from FactoryArguments object.
     */
    private function getAdditionalParameter(\ReflectionClass $reflection, FactoryArguments $args) {

        $constructor = $reflection->getConstructor();
        $constructorParameters = $constructor->getParameters();

        $constructorParameter = $constructorParameters[0];
        $name = $constructorParameter->getName();

        $name = Inflector::camelize($name);

        if ($args->$name === null) {
            throw new \LogicException($name . ' should not be null');
        }

        return $args->$name;
    }

    /**
     * @param \ReflectionClass $reflection
     * @return bool
     * @author Lukasz Mordawski <lukasz.mordawski@gmail.com>
     *
     * This method checks if additional parameter should be passed to wrapper constructor
     */
    private function isAdditionalParameterNeeded(\ReflectionClass $reflection) {

        $constructor = $reflection->getConstructor();
        $constructorParameters = $constructor->getParameters();

        $constructorParameter = $constructorParameters[0];
        $name = $constructorParameter->getName();

        return $name != 'auth_details';

    }

    /**
     * @param $endpoint
     * @return string
     * @author Lukasz Mordawski <lukasz.mordawski@gmail.com>
     *
     * This method returns wrapper class name.
     */
    private function getClassName($endpoint) {
        $endpoint = ucfirst(strtolower($endpoint));
        return 'CS_REST_' . $endpoint;
    }

}