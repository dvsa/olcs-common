<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class BusRegStatusFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return BusRegStatus
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $translator = $container->get('translator');
        $viewHelperManager = $container->get('ViewHelperManager');
        return new BusRegStatus($translator, $viewHelperManager);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return BusRegStatus
     */
    public function createService(ServiceLocatorInterface $serviceLocator): BusRegStatus
    {
        return $this->__invoke($serviceLocator, BusRegStatus::class);
    }
}
