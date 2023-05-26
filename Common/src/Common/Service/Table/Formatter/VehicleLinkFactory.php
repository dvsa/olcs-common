<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class VehicleLinkFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return VehicleLink
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        return new VehicleLink($urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return VehicleLink
     */
    public function createService(ServiceLocatorInterface $serviceLocator): VehicleLink
    {
        return $this->__invoke($serviceLocator, VehicleLink::class);
    }
}
