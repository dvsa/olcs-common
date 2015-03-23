<?php

/**
 * Business Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService;

use Zend\ServiceManager\Config;
use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Business Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessServiceManagerFactory extends AbstractPluginManagerFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $configObject = new Config($config['business_service_manager']);

        $plugins = new BusinessServiceManager($configObject);
        $plugins->setServiceLocator($serviceLocator);

        return $plugins;
    }
}
