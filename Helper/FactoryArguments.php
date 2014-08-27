<?php

namespace LukaszMordawski\CampaignMonitorBundle\Helper;

/**
 * Class FactoryArguments
 * @package Stevens\MainBundle\Service\CampaignMonitor
 * @author Lukasz Mordawski <lukasz.mordawski@gmail.com>
 *
 * Container for settings used to construct CreateSend REST API wrappers
 */
class FactoryArguments
{
    /** @var String */
    public $clientId;

    /** @var Integer */
    public $campaignId;

    /** @var Integer */
    public $listId;

    /** @var Integer */
    public $segmentId;

    /** @var Integer */
    public $templateId;

    /** @var String */
    public $endpoint;
}