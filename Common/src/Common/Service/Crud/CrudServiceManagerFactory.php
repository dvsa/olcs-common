<?php

/**
 * Crud Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Crud;

use Zend\ServiceManager\Config;
use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Crud Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CrudServiceManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = '\Common\Service\Crud\CrudServiceManager';

    /**
     * Create and return a plugin manager.
     * Classes that extend this should provide a valid class for
     * the PLUGIN_MANGER_CLASS constant.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return AbstractPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $configObject = new Config($config['crud_service_manager']);

        $pluginManagerClass = static::PLUGIN_MANAGER_CLASS;
        /* @var $plugins AbstractPluginManager */
        $plugins = new $pluginManagerClass($configObject);
        $plugins->setServiceLocator($serviceLocator);
        $configuration = $serviceLocator->get('Config');

        if (isset($configuration['di']) && $serviceLocator->has('Di')) {
            $plugins->addAbstractFactory($serviceLocator->get('DiAbstractServiceFactory'));
        }

        return $plugins;
    }
}
