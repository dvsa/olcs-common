<?php

/**
 * Abstract Controller Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract Controller Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractControllerFactory implements AbstractFactoryInterface
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
        $config = $serviceLocator->getServiceLocator()->get('Config');

        return isset($config['controllers']['lva_controllers'][$requestedName]);
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
        $config =  $serviceLocator->getServiceLocator()->get('Config');

        $class = $config['controllers']['lva_controllers'][$requestedName];

        $controller = new $class;

        return $controller;
    }
}
