<?php

/**
 * Helper Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Helper Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class HelperServiceFactory implements FactoryInterface
{
    /**
     * Holds the service locator
     *
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * Cache the services
     *
     * @var array
     */
    private $helperServices = array();

    /**
     * Create the helper service factory
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Common\Service\Helper\HelperServiceFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get the instance of the helper service
     *
     * @param string $serviceName
     * @return \Common\Service\Helper\HelperServiceInterface
     */
    public function getHelperService($serviceName)
    {
        if (!isset($this->helperServices[$serviceName])) {
            $className = __NAMESPACE__ . '\\' . $serviceName . 'Service';
            $this->helperServices[$serviceName] = new $className();
            $this->helperServices[$serviceName]->setServiceLocator($this->serviceLocator);
            $this->helperServices[$serviceName]->setHelperServiceFactory($this);
        }

        return $this->helperServices[$serviceName];
    }
}
