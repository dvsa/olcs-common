<?php

namespace Common\Service\Data;

use Laminas\Mvc\Service\AbstractPluginManagerFactory;
use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * PluginManagerFactory
 */
class PluginManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'Common\Service\Data\PluginManager';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = parent::createService($serviceLocator);

        $config = $serviceLocator->get('Config');

        if (isset($config['data_services'])) {
            $pluginManagerConfig = new ServiceManagerConfig($config['data_services']);
            $pluginManagerConfig->configureServiceManager($service);
        }

        return $service;
    }
}
