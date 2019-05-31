<?php

namespace Common\Service\Qa;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TextFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TextFieldsetPopulatorFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new TextFieldsetPopulator(
            $serviceLocator->get('QaTextFactory'),
            $serviceLocator->get('QaTranslateableTextHandler')
        );
    }
}
