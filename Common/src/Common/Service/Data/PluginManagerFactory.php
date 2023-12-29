<?php

namespace Common\Service\Data;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Service\AbstractPluginManagerFactory;
use Laminas\Mvc\Service\ServiceManagerConfig;

/**
 * PluginManagerFactory
 */
class PluginManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'Common\Service\Data\PluginManager';

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $service = parent::__invoke($container, $name, $options);

        $config = $container->get('Config');

        if (isset($config['data_services'])) {
            $pluginManagerConfig = new ServiceManagerConfig($config['data_services']);
            $pluginManagerConfig->configureServiceManager($service);
        }

        return $service;
    }
}
