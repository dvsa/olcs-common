<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PermitUsageDataHandlerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PermitUsageDataHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new PermitUsageDataHandler(
            $serviceLocator->get('QaCommonIsValidBasedWarningAdder'),
            $serviceLocator->get('QaBilateralPermitUsageIsValidHandler')
        );
    }
}
