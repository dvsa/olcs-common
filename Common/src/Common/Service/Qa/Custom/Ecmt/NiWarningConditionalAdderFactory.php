<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NiWarningConditionalAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NiWarningConditionalAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NiWarningConditionalAdder(
            $serviceLocator->get('QaCommonWarningAdder')
        );
    }
}
