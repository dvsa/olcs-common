<?php

namespace Common\Service\Qa;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DateNotInPastValidatorFactory implements FactoryInterface
{
    /** @var array */
    private $options;

    /**
     * Create factory instance (used by zf2 to pass in options from input specification)
     *
     * @param array $options
     *
     * @return DateNotInPastValidatorFactory
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

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
            $serviceLocator->getServiceLocator()->get('QaDateTimeFactory'),
            $this->options
        );
    }
}
