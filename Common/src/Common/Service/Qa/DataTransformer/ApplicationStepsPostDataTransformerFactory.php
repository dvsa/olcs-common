<?php

namespace Common\Service\Qa\DataTransformer;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApplicationStepsPostDataTransformerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApplicationStepsPostDataTransformer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ApplicationStepsPostDataTransformer(
            $serviceLocator->get('QaDataTransformerProvider')
        );
    }
}
