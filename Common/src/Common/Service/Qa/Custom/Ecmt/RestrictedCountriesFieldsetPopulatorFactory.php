<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RestrictedCountriesFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RestrictedCountriesFieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new RestrictedCountriesFieldsetPopulator(
            $serviceLocator->get('QaEcmtYesNoRadioFactory'),
            $serviceLocator->get('QaEcmtRestrictedCountriesMultiCheckboxFactory')
        );
    }
}
