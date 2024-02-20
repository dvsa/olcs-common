<?php

namespace Common\Service\Section\VehicleSafety\Vehicle\Formatter;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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

        $viewHelperManager = $container->get('ViewHelperManager');
        return new Vrm($viewHelperManager);
    }
}
