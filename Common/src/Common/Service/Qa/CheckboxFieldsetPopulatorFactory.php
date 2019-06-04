<?php

namespace Common\Service\Qa;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CheckboxFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CheckboxFieldsetPopulatorFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CheckboxFieldsetPopulator(
            $serviceLocator->get('QaCheckboxFactory'),
            $serviceLocator->get('QaTranslateableTextHandler')
        );
    }
}
