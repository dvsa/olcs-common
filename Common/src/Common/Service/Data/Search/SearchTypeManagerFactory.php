<?php

namespace Common\Service\Data\Search;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Config as ServiceManagerConfig;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class SearchTypeManagerFactory
 * @package Olcs\Service\Data\Search
 */
class SearchTypeManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchTypeManager
    {
        $service = new SearchTypeManager();

        $config = $container->get('Config');
        if (isset($config['search'])) {
            $configuration = new ServiceManagerConfig($config['search']);
            $configuration->configureServiceManager($service);
        }

        return $service;
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SearchTypeManager
    {
        return $this->__invoke($serviceLocator, SearchTypeManager::class);
    }
}
