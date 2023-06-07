<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TaskAllocationUserFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TaskAllocationUser
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $dataHelper = $container->get('Helper\Data');
        return new TaskAllocationUser($dataHelper);
    }

    public function createService(ServiceLocatorInterface $serviceLocator): TaskAllocationUser
    {
        return $this->__invoke($serviceLocator, TaskAllocationUser::class);
    }
}
