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
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $configObject = new Config($config['business_rule_manager']);

        $plugins = new BusinessRuleManager($configObject);
        $plugins->setServiceLocator($serviceLocator);

        return $plugins;
    }
}
