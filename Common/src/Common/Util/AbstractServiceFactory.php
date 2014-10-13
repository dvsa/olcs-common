<?php

/**
 * Abstract Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Util;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Abstract Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return class_exists($this->getClassName($requestedName));
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $serviceClassName = $this->getClassName($requestedName);

        $service = new $serviceClassName();

        $this->injectDependencies($service, $serviceLocator);

        return $service;
    }

    /**
     * Inject the service dependencies
     *
     * @param object $service
     * @param object $serviceLocator
     */
    private function injectDependencies($service, $serviceLocator)
    {
        if ($service instanceof ServiceLocatorAwareInterface) {
            $service->setServiceLocator($serviceLocator);
        }
    }

    /**
     * Get the class name from the service name
     *
     * Helper\Access becomes Common\Service\Helper\AccessHelperService
     * Access becomes Common\Service\Access
     *
     * @param string $name
     * @return string
     */
    private function getClassName($name)
    {
        $className = 'Common\Service\\';

        if (strstr($name, '\\')) {
            list($type, $name) = explode('\\', $name, 2);

            return $className . $type . '\\' . $name . $type . 'Service';
        }

        return $className . $name;
    }
}
