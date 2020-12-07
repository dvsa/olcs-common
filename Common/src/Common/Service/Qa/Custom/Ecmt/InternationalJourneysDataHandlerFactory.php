<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class InternationalJourneysDataHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return InternationalJourneysDataHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new InternationalJourneysDataHandler(
            $serviceLocator->get('QaCommonIsValidBasedWarningAdder'),
            $serviceLocator->get('QaEcmtInternationalJourneysIsValidHandler')
        );
    }
}
