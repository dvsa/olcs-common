<?php

namespace Common\Service\Qa\DataTransformer;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DataTransformerProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DataTransformerProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dataTransformerProvider = new DataTransformerProvider();

        $dataTransformerProvider->registerTransformer(
            'number-of-permits-either',
            $serviceLocator->get('QaEcmtNoOfPermitsSingleDataTransformer')
        );

        $dataTransformerProvider->registerTransformer(
            'number-of-permits-both',
            $serviceLocator->get('QaEcmtNoOfPermitsSingleDataTransformer')
        );

        return $dataTransformerProvider;
    }
}
