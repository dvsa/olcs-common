<?php

namespace Common\Service\Section\VehicleSafety\Vehicle\Formatter;

use Common\Service\Table\Formatter\Address;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class VrmFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return Vrm
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $viewHelperManager = $container->get('viewhelpermanager');
        return new Vrm($viewHelperManager);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Vrm
     */
    public function createService(ServiceLocatorInterface $serviceLocator): Vrm
    {
        return $this->__invoke($serviceLocator, Vrm::class);
    }
}
