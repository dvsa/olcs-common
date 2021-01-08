<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class StandardYesNoValueOptionsGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StandardYesNoValueOptionsGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new StandardYesNoValueOptionsGenerator(
            $serviceLocator->get('QaBilateralYesNoValueOptionsGenerator')
        );
    }
}
