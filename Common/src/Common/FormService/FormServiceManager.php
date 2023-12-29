<?php

namespace Common\FormService;

use Laminas\Mvc\Controller\PluginManager;
use Laminas\ServiceManager\ConfigInterface;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\RuntimeException;

/**
 * Form Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormServiceManager extends PluginManager
{
    // The Abstract Factory in common, selfserve and internal validates the requested plugins
    public function validate($instance)
    {
        return;
    }

    public function __construct($container, array $config = null)
    {
        parent::__construct($container, $config);
    }
}
