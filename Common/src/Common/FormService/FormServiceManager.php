<?php

/**
 * Form Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService;

use Dvsa\Olcs\Utils\Traits\PluginManagerTrait;
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
    use PluginManagerTrait;

    protected $instanceOf = FormServiceInterface::class;

    public function __construct(ConfigInterface $config = null)
    {
        if ($config) {
            $config->configureServiceManager($this);
        }

        $this->addInitializer(
            new FormServiceManagerInitializer()
        );
    }
}
