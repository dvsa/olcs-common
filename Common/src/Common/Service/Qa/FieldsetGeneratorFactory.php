<?php

namespace Common\Service\Qa;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FieldsetGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FieldsetGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new FieldsetGenerator(
            $serviceLocator->get('QaFieldsetPopulatorProvider'),
            $serviceLocator->get('QaFieldsetFactory')
        );
    }
}
