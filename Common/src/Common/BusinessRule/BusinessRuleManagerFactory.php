<?php

/**
 * Business Rule Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessRule;

use Zend\ServiceManager\Config;
use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Business Rule Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessRuleManagerFactory extends AbstractPluginManagerFactory
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
        $configObject = new Config($config['business_rule_manager']);

        $plugins = new BusinessRuleManager($configObject);
        $plugins->setServiceLocator($serviceLocator);

        return $plugins;
    }
}
