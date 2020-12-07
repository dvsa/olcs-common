<?php

namespace Common\Service\Qa;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FormattedTranslateableTextParametersGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TranslateableTextHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new FormattedTranslateableTextParametersGenerator(
            $serviceLocator->get('QaTranslateableTextParameterHandler')
        );
    }
}
