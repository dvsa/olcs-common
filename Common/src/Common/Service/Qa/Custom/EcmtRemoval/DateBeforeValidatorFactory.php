<?php

namespace Common\Service\Qa\Custom\EcmtRemoval;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DateBeforeValidatorFactory implements FactoryInterface
{
    /** @var array */
    private $options;

    /**
     * Create factory instance (used by zf2 to pass in options from input specification)
     *
     * @param array $options
     *
     * @return DateBeforeValidatorFactory
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
     * @return DateBeforeValidator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        return new DateBeforeValidator(
            $mainServiceLocator->get('ViewHelperManager')->get('DateFormat'),
            $mainServiceLocator->get('QaDateTimeFactory'),
            $this->options
        );
    }
}
