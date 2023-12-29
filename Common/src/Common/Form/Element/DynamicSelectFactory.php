<?php

namespace Common\Form\Element;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DynamicSelectFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DynamicSelect
    {
        $dataServiceManager = $container->get('DataServiceManager');
        return new DynamicSelect($dataServiceManager);
    }
}
