<?php

namespace Common\Service\Api;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

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
