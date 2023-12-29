<?php

namespace Common\Form\Element;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DynamicMultiCheckboxFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DynamicMultiCheckbox
    {
        $dataServiceManager = $container->get('DataServiceManager');
        return new DynamicMultiCheckbox($dataServiceManager);
    }
}
