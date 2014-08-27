CampaignMonitorBundle
=====================
This bundle was make, when I needed to use REST API wrapper for Campaign Monitor ([campaignmonitor/createsend-php](https://packagist.org/packages/campaignmonitor/createsend-php)).
That was some work to have it connected to Symfony 2, so I decided to publish results. This bundle will allow you to
easy use that API, without care about exact class names and needed parameters.
BTW. This is first Symfony 2 vendor bundle I ever made. Every notes, ideas and bugfixes are welcome.

Installation
------------
Require [lukaszmordawski/campaign-monitor-bundle](https://packagist.org/packages/lukaszmordawski/campaign-monitor-bundle) into your `composer.json` file:

``` json
{
    "require": {
        "lukaszmordawski/campaign-monitor-bundle": "1.0.x-dev"
    }
}
```

Then include bundle in your kernel:

``` php
# app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            # ...
            new LukaszMordawski\CampaignMonitorBundle\LukaszMordawskiCampaignMonitorBundle()
        );
    }
```

The last step is to do some configuration in your `app/config/config.yml` file:

``` yml
# app/config/config.yml
lukasz_mordawski_campaign_monitor:
    api_key: YOUR_API_KEY
    client_id: YOUR_CLIENT_ID // don't worry, you can pass another on making API call, if needed
    cache_service_id: CACHE_SERVICE_ID // optional, default cache is already configured in the bundle
    cache_lifetime: CACHE_LIFETIME // optional, default value is 3600 (1 hour)
```

Usage:
------

``` php
# src/Acme/DefaultBundle/Controller/DefaultController.php
...
use LukaszMordawski\CampaignMonitorBundle\Helper\FactoryArguments;
...
        $arguments = new FactoryArguments; // by this object you can pass various parameters
        $arguments->endpoint = 'clients';
        
        $data = $this->get('campaign_monitor.invoker')
            ->invoke($arguments, 'getCampaigns');
...
```

If wrapper method need any parameters you can pass them as array as third parameter of invoke() method.
This bundle is only facade to original library to make it easy to use with Symfony 2 project, so for any questions regarding API or wrappers please refer to [original package docs](https://github.com/campaignmonitor/createsend-php/blob/master/README.md)