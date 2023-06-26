<?php

namespace Common\Service\Qa;

use Interop\Container\ContainerInterface;
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

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DateNotInPastValidator
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        return new DateNotInPastValidator(
            $container->get('QaDateTimeFactory'),
            $this->options
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DateNotInPastValidator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): DateNotInPastValidator
    {
        return $this->__invoke($serviceLocator, DateNotInPastValidator::class);
    }
}
