<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AnnualTripsAbroadDataHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AnnualTripsAbroadDataHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new AnnualTripsAbroadDataHandler(
            $serviceLocator->get('QaCommonIsValidBasedWarningAdder'),
            $serviceLocator->get('QaEcmtShortTermAnnualTripsAbroadIsValidHandler')
        );
    }
}
