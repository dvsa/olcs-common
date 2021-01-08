<?php

namespace Common\Service\Qa;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class RadioFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RadioFieldsetPopulatorFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new RadioFieldsetPopulator(
            $serviceLocator->get('QaRadioFactory'),
            $serviceLocator->get('QaTranslateableTextHandler')
        );
    }
}
