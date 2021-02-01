<?php

/**
 * Form Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\RuntimeException;
use Laminas\ServiceManager\ConfigInterface;

/**
 * Form Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormServiceManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $config = null)
    {
        if ($config) {
            $config->configureServiceManager($this);
        }

        $this->addInitializer(
            new FormServiceManagerInitializer()
        );
    }

    public function validatePlugin($plugin)
    {
        if (!$plugin instanceof FormServiceInterface) {
            throw new RuntimeException('Form service does not implement FormServiceInterface');
        }
    }
}
