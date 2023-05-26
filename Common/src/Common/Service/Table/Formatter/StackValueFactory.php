<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class StackValueFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return StackValue
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $stackHelper = $container->get('Helper\Stack');
        return new StackValue($stackHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StackValue
     */
    public function createService(ServiceLocatorInterface $serviceLocator): StackValue
    {
        return $this->__invoke($serviceLocator, StackValue::class);
    }
}
