<?php

namespace Common\Data\Mapper\Lva;

use Common\Service\Table\Formatter\FormatterPluginManager;
use Common\Service\Table\Formatter\VehicleDiscNo;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class GoodsVehiclesVehicleFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GoodsVehiclesVehicle
    {
        return new GoodsVehiclesVehicle(
            $container->get(FormatterPluginManager::class)->get(VehicleDiscNo::class)
        );
    }
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, GoodsVehiclesVehicle::class);
    }
}
