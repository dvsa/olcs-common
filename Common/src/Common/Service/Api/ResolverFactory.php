<?php

namespace Common\Service\Api;

use Laminas\Mvc\Service\AbstractPluginManagerFactory;
use Laminas\Mvc\Service\ServiceManagerConfig;
use Psr\Container\ContainerInterface;

class ResolverFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = Resolver::class;

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $service = parent::__invoke($container, $name, $options);

        $config = $container->get('Config');

        if (isset($config['rest_services'])) {
            $pluginManagerConfig = new ServiceManagerConfig($config['rest_services']);
            $pluginManagerConfig->configureServiceManager($service);
        }

        return $service;
    }
}
