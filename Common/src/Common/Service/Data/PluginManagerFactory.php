<?php

namespace Common\Service\Data;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceLocatorInterface;

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
