<?php

namespace Common\Service\Qa\DataTransformer;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
