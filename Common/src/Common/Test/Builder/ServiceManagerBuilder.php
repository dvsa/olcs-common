<?php

namespace Common\Test\Builder;

use Laminas\ServiceManager\ServiceManager;

class ServiceManagerBuilder implements BuilderInterface
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
     * @inheritDoc
     */
    public function build()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setAllowOverride(true);
        if (is_callable($this->servicesProvider)) {
            $services = call_user_func($this->servicesProvider, $serviceManager);
        } else {
            $services = $this->servicesProvider->setUpDefaultServices($serviceManager);
        }
        foreach ($services as $serviceName => $service) {
            $serviceManager->setService($serviceName, $service);
        }
        return $serviceManager;
    }
}
