<?php

/**
 * Form Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService;

use Zend\ServiceManager\Config;
use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Form Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CrudServiceManagerFactory extends AbstractPluginManagerFactory
{
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
        $configObject = new Config($config['form_service_manager']);

        $plugins = new FormServiceManager($configObject);
        $plugins->setServiceLocator($serviceLocator);

        return $plugins;
    }
}
