<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
            $serviceLocator->get('ViewHelperManager')->get('partial'),
            $serviceLocator->get('QaEcmtShortTermInternationalJourneysIsValidHandler')
        );
    }
}
