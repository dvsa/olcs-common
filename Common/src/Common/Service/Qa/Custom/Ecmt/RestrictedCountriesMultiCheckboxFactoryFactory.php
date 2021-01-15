<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class RestrictedCountriesMultiCheckboxFactoryFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RestrictedCountriesMultiCheckboxFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        return new RestrictedCountriesMultiCheckboxFactory($config['application_context']);
    }
}
