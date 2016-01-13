<?php

namespace Common\Service\Data;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class BundleManagerFactory
 * @package Common\Service\Data
 */
class BundleManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'Common\Service\Data\BundleManager';

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Zend\ServiceManager\AbstractPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = parent::createService($serviceLocator);

        $config = $serviceLocator->get('Config');

        if (isset($config['bundles'])) {
            $pluginManagerConfig = new ServiceManagerConfig($config['bundles']);
            $pluginManagerConfig->configureServiceManager($service);
        }

        return $service;
    }
}
