<?php

/**
 * Abstract Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Util;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\AbstractFactoryInterface;

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
        return ($this->getClassName($requestedName) !== false);
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

        return $service;
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
