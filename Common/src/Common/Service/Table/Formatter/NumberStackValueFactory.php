<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NumberStackValueFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NumberStackValue
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $stackHelper = $container->get('Helper\Stack');
        return new NumberStackValue($stackHelper);
    }

    public function createService(ServiceLocatorInterface $serviceLocator): NumberStackValue
    {
        return $this->__invoke($serviceLocator, NumberStackValue::class);
    }
}
