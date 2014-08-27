<?php

namespace LukaszMordawski\CampaignMonitorBundle\Service;

use Doctrine\Common\Inflector\Inflector;
use LukaszMordawski\CampaignMonitorBundle\Exception\Exception;
use LukaszMordawski\CampaignMonitorBundle\Helper\FactoryArguments;

/**
 * Class Invoker
 * @package LukaszMordawski\CampaignMonitorBundle\Service
 * @author Lukasz Mordawski <lukasz.mordawski@gmail.com>
 *
 * This class is used to invoke proper CreateSend API method.
 * In fact, this is facade for all CreateSend wrappers from external library.
 */
class Invoker
{
    /** @var \LukaszMordawski\CampaignMonitorBundle\Service\Factory  */
    private $factory;

    /** @var String */
    private $clientId;

    /** @var Integer */
    private $cacheLifetime;

    /**
     * @param Factory $factory
     * @param \Doctrine\Common\Cache\Cache $cache
     * @param $clientId
     * @param $cacheLifetime
     * @author Lukasz Mordawski <lukasz.mordawski@gmail.com>
     *
     * Constructor
     */
    public function __construct(Factory $factory, \Doctrine\Common\Cache\Cache $cache, $clientId, $cacheLifetime)
    {
        $this->factory = $factory;
        $this->cache = $cache;
        $this->clientId = $clientId;
        $this->cacheLifetime = $cacheLifetime;
    }

    /**
     * @param FactoryArguments $arguments
     * @param $method
     * @param array $parameters
     * @return mixed
     * @throws \LukaszMordawski\CampaignMonitorBundle\Exception\Exception
     * @throws \LogicException
     *
     * This method invokes proper API method and stores result in cache.
     */
    public function invoke(FactoryArguments $arguments, $method, $parameters = [])
    {

        if (!$arguments->clientId)
            $arguments->clientId = $this->clientId;
        $method = Inflector::tableize($method);

        $cacheKey = $this->getCacheKey($arguments, $method, $parameters);
        if ($this->cache->contains($cacheKey)) {
            return unserialize($this->cache->fetch($cacheKey));
        }

        $csClient = $this->factory->factory($arguments);

        if (method_exists($csClient, $method)) {
            $data = call_user_func_array([$csClient, $method], $parameters);
        }
        else {
            throw new \LogicException(
                sprintf('Method %s does not exist for class %s', $method, get_class($csClient))
            );
        }

        if ($data->http_status_code != 200) {
            throw new Exception($data->response->Message, $data->response->Code);
        }

        $this->cache->save($cacheKey, serialize($data->response), $this->cacheLifetime);

        return $data->response;
    }

    /**
     * @param FactoryArguments $arguments
     * @param $method
     * @param $parameters
     * @return string
     * @author Lukasz Mordawski <lukasz.mordawski@gmail.com>
     *
     * This method creates cache key from given arguments.
     */
    private function getCacheKey(FactoryArguments $arguments, $method, $parameters)
    {
        return
            'campaignmonitor' .
            md5(serialize($arguments)) .
            $method .
            md5(serialize($parameters));
    }

}