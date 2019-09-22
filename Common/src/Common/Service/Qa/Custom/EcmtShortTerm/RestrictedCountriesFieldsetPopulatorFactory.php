<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

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
            $serviceLocator->get('QaEcmtShortTermYesNoRadioFactory'),
            $serviceLocator->get('QaEcmtShortTermRestrictedCountriesMultiCheckboxFactory')
        );
    }
}
