<?php

declare(strict_types=1);

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UnlicensedVehicleWeightFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): UnlicensedVehicleWeight
    {
        $stackHelper = $container->get('Helper\Stack');
        return new UnlicensedVehicleWeight($stackHelper);
    }
}
