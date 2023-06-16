<?php

namespace Common\FormService;

use Dvsa\Olcs\Utils\Traits\PluginManagerTrait;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ConfigInterface;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\RuntimeException;

/**
 * Form Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormServiceManager extends AbstractPluginManager
{
    // The Abstract Factory in common, selfserve and internal validates the requested plugins
    public function validate($instance)
    {
        return;
    }

    /**
     * {@inheritDoc}
     *
     * This method is required to validate plugin for laminas-servicemanager 2.x
     * https://github.com/laminas/laminas-servicemanager/blob/2.7.11/src/AbstractPluginManager.php#L128
     *
     * @todo To be removed as part of OLCS-28149
     */
    public function validatePlugin($plugin)
    {
        try {
            $this->validate($plugin);
        } catch (InvalidServiceException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function __construct(ConfigInterface $config = null)
    {
        if ($config) {
            $config->configureServiceManager($this);
        }
        parent::__construct($config);
    }
}
