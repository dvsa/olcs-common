<?php

namespace Common\Service\Qa\Custom\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class IsValidBasedWarningAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IsValidBasedWarningAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new IsValidBasedWarningAdder(
            $serviceLocator->get('QaCommonWarningAdder')
        );
    }
}
