<?php

namespace Common\Util;

use Laminas\ServiceManager\AbstractFactoryInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
     * @param \Laminas\ServiceManager\ServiceLocatorInterface $serviceLocator Service manager
     * @param string                                       $name           Name
     * @param string                                       $requestedName  Class Name
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return ($this->getClassName($requestedName) !== false);
    }

    /**
     * Create service with name
     *
     * @param \Laminas\ServiceManager\ServiceLocatorInterface $serviceLocator Service manager
     * @param string                                       $name           Name
     * @param string                                       $requestedName  Class Name
     *
     * @return object
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $serviceClassName = $this->getClassName($requestedName);

        $service = new $serviceClassName();

        if ($service instanceof FactoryInterface) {
            return $service->createService($serviceLocator);
        }

        return $service;
    }

    /**
     * Get the class name from the service name
     *
     * Helper\Access becomes Common\Service\Helper\AccessHelperService
     * Access becomes Common\Service\Access
     *
     * @param string $name Class name
     *
     * @return string
     */
    protected function getClassName($name)
    {
        $namespaces = [
            'Olcs\Service\\',
            'Admin\Service\\',
            'Common\Service\\'
        ];

        if (strstr($name, '\\')) {
            list($type, $name) = explode('\\', $name, 2);

            foreach ($namespaces as $namespace) {
                $className = $namespace . $type . '\\' . $name . $type . 'Service';
                if (class_exists($className)) {
                    return $className;
                }
            }
        }

        return false;
    }
}
