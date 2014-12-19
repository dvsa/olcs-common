<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\BundleAware;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DataServiceAbstractFactory
 * @package Common\Service\Data
 */
class DataServiceAbstractFactory implements AbstractFactoryInterface
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
        return (strpos($requestedName, 'Generic\Service\Data') !== false);
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
        $name = trim(str_replace('Generic\Service\Data', '', $requestedName), '\\');

        $service = new Generic();
        $service->setServiceName($name);

        if ($service instanceof BundleAware) {
            $bundle = $serviceLocator->getServiceLocator()->get('BundleManager')->get($service->getDefaultBundleName());
            $service->setDefaultBundle($bundle);
        }

        return $service;
    }
}
