<?php

/**
 * Business Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\RuntimeException;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Common\BusinessRule\BusinessRuleAwareInterface;

/**
 * Business Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessServiceManager extends AbstractPluginManager
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
        if ($instance instanceof BusinessServiceAwareInterface) {
            $instance->setBusinessServiceManager($this);
        }

        if ($instance instanceof BusinessRuleAwareInterface) {
            $instance->setBusinessRuleManager($this->getServiceLocator()->get('BusinessRuleManager'));
        }

        if ($instance instanceof ServiceLocatorAwareInterface) {
            $instance->setServiceLocator($this->getServiceLocator());
        }
    }

    public function validatePlugin($plugin)
    {
        if (!$plugin instanceof BusinessServiceInterface) {
            throw new RuntimeException('Business service does not implement BusinessServiceInterface');
        }
    }
}
