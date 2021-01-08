<?php

namespace Common\Service\Qa;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TranslateableTextHandlerFactory implements FactoryInterface
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
        return new TranslateableTextHandler(
            $serviceLocator->get('QaFormattedTranslateableTextParametersGenerator'),
            $serviceLocator->get('Helper\Translation')
        );
    }
}
