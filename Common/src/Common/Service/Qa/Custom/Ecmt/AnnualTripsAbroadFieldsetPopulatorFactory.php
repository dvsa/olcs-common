<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AnnualTripsAbroadFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AnnualTripsAbroadFieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new AnnualTripsAbroadFieldsetPopulator(
            $serviceLocator->get('QaTextFieldsetPopulator'),
            $serviceLocator->get('Helper\Translation'),
            $serviceLocator->get('QaEcmtNiWarningConditionalAdder'),
            $serviceLocator->get('QaCommonHtmlAdder')
        );
    }
}
