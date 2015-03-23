<?php

/**
 * Business Rule Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessRule;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\RuntimeException;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Business Rule Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessRuleManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $config = null)
    {
        if ($config) {
            $config->configureServiceManager($this);
        }

        $this->addInitializer(array($this, 'initialize'));
    }

    public function initialize($instance)
    {
        if ($instance instanceof ServiceLocatorAwareInterface) {
            $instance->setServiceLocator($this->getServiceLocator());
        }
    }

    public function validatePlugin($plugin)
    {
        if (!$plugin instanceof BusinessRuleInterface) {
            throw new RuntimeException('Business rule does not implement BusinessRuleInterface');
        }
    }
}
