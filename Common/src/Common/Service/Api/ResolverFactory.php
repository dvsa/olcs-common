<?php

namespace Common\Service\Api;

use Laminas\Mvc\Service\AbstractPluginManagerFactory;
use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class ResolverFactory
 * @package Common\Service\Api
 */
class ResolverFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'Common\Service\Api\Resolver';

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return AbstractPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = parent::createService($serviceLocator);

        $config = $serviceLocator->get('Config');

        if (isset($config['rest_services'])) {
            $pluginManagerConfig = new ServiceManagerConfig($config['rest_services']);
            $pluginManagerConfig->configureServiceManager($service);
        }

        return $service;
    }
}
