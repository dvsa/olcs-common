<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StandardAndCabotageIsValidHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StandardAndCabotageIsValidHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StandardAndCabotageIsValidHandler(
            $serviceLocator->get('QaBilateralStandardAndCabotageSubmittedAnswerGenerator')
        );
    }
}
