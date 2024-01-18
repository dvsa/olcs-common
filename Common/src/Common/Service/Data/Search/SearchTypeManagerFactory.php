<?php

namespace Common\Service\Data\Search;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Config as ServiceManagerConfig;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
}
