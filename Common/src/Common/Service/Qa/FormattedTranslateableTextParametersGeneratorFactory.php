<?php

namespace Common\Service\Qa;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
