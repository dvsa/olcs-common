<?php

/**
 * Abstract Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Util;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Abstract Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractFactory implements FactoryInterface
{
    /**
     * Holds the service namespace
     *
     * @var string
     */
    protected $namespace = __NAMESPACE__;

    /**
     * Holds the service manager
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * Cache the service instances
     *
     * @var array
     */
    protected $serviceInstances = array();

    /**
     * Setup the factory, with a service locator
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceManager = $serviceLocator;

        return $this;
    }

    /**
     * Return an instance of service
     *
     * @param string $service
     * @return object
     */
    public function get($service)
    {
        if (!isset($this->serviceInstances[$service])) {
            $this->serviceInstances[$service] = $this->create($service);
        }

        return $this->serviceInstances[$service];
    }

    /**
     * Create an instance of the service
     *
     * @param string $service
     * @return object
     * @throws \Exception
     */
    public function create($service)
    {
        $className = $this->namespace . '\\' . $service;

        if (!class_exists($className)) {

            $className .= 'Service';

            if (!class_exists($className)) {
                throw new \Exception('Service not found: ' . $service);
            }
        }

        $serviceObject = new $className;

        if ($serviceObject instanceof ServiceLocatorAwareInterface) {
            $serviceObject->setServiceLocator($this->serviceManager);
        }

        return $serviceObject;
    }
}
