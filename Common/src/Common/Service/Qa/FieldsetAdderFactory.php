<?php

namespace Common\Service\Qa;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
