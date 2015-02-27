<?php

/**
 * Crud Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Crud;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\RuntimeException;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Crud Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CrudServiceManager extends AbstractPluginManager
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

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceof CrudServiceInterface) {
            throw new RuntimeException('Crud service does not implement CrudServiceInterface');
        }
    }
}
