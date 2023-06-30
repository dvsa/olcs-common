<?php

declare(strict_types=1);

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class UnlicensedVehicleWeightFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): UnlicensedVehicleWeight
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $stackHelper = $container->get('Helper\Stack');
        return new UnlicensedVehicleWeight($stackHelper);
    }

    public function createService(ServiceLocatorInterface $serviceLocator): UnlicensedVehicleWeight
    {
        return $this->__invoke($serviceLocator, UnlicensedVehicleWeight::class);
    }
}
