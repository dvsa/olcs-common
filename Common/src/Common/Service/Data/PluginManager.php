<?php

namespace Common\Service\Data;

use Laminas\ServiceManager\AbstractPluginManager;

/**
 * Class PluginManager
 * @package Common\Service\Data
 */
class PluginManager extends AbstractPluginManager
{
    /**
     * @inheritdoc
     */
    public function __construct($configOrContainerInstance = null, array $v3config = [])
    {
        parent::__construct($configOrContainerInstance, $v3config);

        $this->addInitializer(
            new RestClientAwareInitializer()
        );
    }

    /**
     * Validate the plugin
     *
     * @param mixed $plugin
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validatePlugin($plugin)
    {
        return true; //for now. not sure how to validate
    }

    /**
     * For BC purposes, check the main service locator for the requested service first; this ensures any registered
     * factories etc are run on services created prior to this class being created.
     *
     * @param string $name
     * @param array $options
     * @param bool $usePeeringServiceManagers
     * @return array|object
     */
    public function get($name, $options = array(), $usePeeringServiceManagers = true)
    {
        if ($this->getServiceLocator()->has($name)) {
            return $this->getServiceLocator()->get($name);
        }
        return parent::get($name, $options, $usePeeringServiceManagers);
    }
}
