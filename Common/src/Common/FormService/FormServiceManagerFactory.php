<?php

namespace Common\FormService;

use Laminas\Mvc\Service\AbstractPluginManagerFactory;
use Laminas\ServiceManager\Config;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Form Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormServiceManagerFactory extends AbstractPluginManagerFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $configObject = new Config($config['form_service_manager']);

        $plugins = new FormServiceManager($configObject);
        $plugins->setServiceLocator($serviceLocator);

        return $plugins;
    }
}
