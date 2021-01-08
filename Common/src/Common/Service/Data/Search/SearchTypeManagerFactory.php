<?php

namespace Common\Service\Data\Search;

use Laminas\ServiceManager\Config as ServiceManagerConfig;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class SearchTypeManagerFactory
 * @package Olcs\Service\Data\Search
 */
class SearchTypeManagerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new SearchTypeManager();

        $config = $serviceLocator->get('Config');
        if (isset($config['search'])) {
            $configuration = new ServiceManagerConfig($config['search']);
            $configuration->configureServiceManager($service);
        }

        return $service;
    }
}
