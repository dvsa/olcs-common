<?php

namespace Common\Service\Qa;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new FieldsetPopulator(
            $serviceLocator->get('QaFieldsetAdder'),
            $serviceLocator->get('QaValidatorsAdder')
        );
    }
}
