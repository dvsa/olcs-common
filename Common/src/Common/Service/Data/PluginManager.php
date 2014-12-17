<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\RestClientAware;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

/**
 * Class PluginManager
 * @package Common\Service\Data
 */
class PluginManager extends AbstractPluginManager
{
    public function __construct()
    {
        parent::__construct();
        $this->addInitializer(array($this, 'initializeRestClientInterface'));
    }

    public function initializeRestClientInterface($instance)
    {
        if ($instance instanceof RestClientAware) {
            $serviceLocator = $this->getServiceLocator();

            /** @var \Common\Util\ResolveApi $apiResolver */
            $apiResolver = $serviceLocator->get('ServiceApiResolver');
            /** @var \Zend\Mvc\I18n\Translator $translator */
            $translator = $serviceLocator->get('translator');

            $client = $apiResolver->getClient($instance->getServiceName());
            $client->setLanguage($translator->getLocale());
            $instance->setRestClient($client);
        }
    }

    /**
     * Validate the plugin
     *
     * @param  mixed $plugin
     * @return bool
     * @throws Exception\RuntimeException if invalid
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
