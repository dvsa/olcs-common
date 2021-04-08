<?php

declare(strict_types=1);

namespace Common\Test\Builder;

use Laminas\ServiceManager\ServiceManager;

class ServiceManagerBuilder
{
    /**
     * @var callable|object
     */
    protected $servicesProvider;

    /**
     * @param callable|object $servicesProvider
     */
    public function __construct($servicesProvider)
    {
        $this->servicesProvider = $servicesProvider;
    }

    /**
     * @return ServiceManager
     */
    public function build(): ServiceManager
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setAllowOverride(true);
        if (is_callable($this->servicesProvider)) {
            $services = call_user_func($this->servicesProvider, $serviceManager);
        } else {
            $services = $this->servicesProvider->setUpDefaultServices($serviceManager);
        }

        // Maintain support for deprecated way of registering services via an array of services. Instead, services
        // should be registered by calling the available setter methods on the ServiceManager instance.
        if (is_array($services)) {
            foreach ($services as $serviceName => $service) {
                $serviceManager->setService($serviceName, $service);
            }
        }

        // Set controller plugin manager to the main service manager so that all services can be resolved from the one
        // service manager instance.
        $serviceManager->setService('ControllerPluginManager', $serviceManager);

        return $serviceManager;
    }
}
