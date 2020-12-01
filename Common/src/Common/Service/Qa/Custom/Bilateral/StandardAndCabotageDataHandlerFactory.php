<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StandardAndCabotageDataHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StandardAndCabotageDataHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StandardAndCabotageDataHandler(
            $serviceLocator->get('QaBilateralStandardAndCabotageSubmittedAnswerGenerator'),
            $serviceLocator->get('QaBilateralStandardAndCabotageIsValidHandler'),
            $serviceLocator->get('QaCommonWarningAdder')
        );
    }
}
