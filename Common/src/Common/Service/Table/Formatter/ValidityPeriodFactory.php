<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ValidityPeriodFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return ValidityPeriod
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $viewHelperManager = $container->get('ViewHelperManager');
        $translator = $container->get('translator');
        return new ValidityPeriod($viewHelperManager, $translator);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ValidityPeriod
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ValidityPeriod
    {
        return $this->__invoke($serviceLocator, ValidityPeriod::class);
    }
}
