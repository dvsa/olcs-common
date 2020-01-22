<?php

namespace Common\Service\Qa;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FieldsetAdderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FieldsetAdder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new FieldsetAdder(
            $serviceLocator->get('QaFieldsetPopulatorProvider'),
            $serviceLocator->get('QaFieldsetFactory'),
            $serviceLocator->get('QaFieldsetModifier')
        );
    }
}
