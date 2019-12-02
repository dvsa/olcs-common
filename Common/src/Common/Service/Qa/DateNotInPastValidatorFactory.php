<?php

namespace Common\Service\Qa;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DateNotInPastValidatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DateNotInPastValidator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new DateNotInPastValidator(
            $serviceLocator->getServiceLocator()->get('QaDateTimeFactory')
        );
    }
}
