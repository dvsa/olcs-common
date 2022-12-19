<?php

namespace Common\Service\Qa\Custom\Common;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DateBeforeValidator
    {
        $mainServiceLocator = $container->getServiceLocator();

        return new DateBeforeValidator(
            $mainServiceLocator->get('ViewHelperManager')->get('DateFormat'),
            $mainServiceLocator->get('QaDateTimeFactory'),
            $this->options
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): DateBeforeValidator
    {
        return $this->__invoke($serviceLocator, DateBeforeValidator::class);
    }
}
