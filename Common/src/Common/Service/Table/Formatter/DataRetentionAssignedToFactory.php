<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DataRetentionAssignedToFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return DataRetentionAssignedTo
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $viewHelperManager = $container->get('viewHelperManager');
        return new DataRetentionAssignedTo($viewHelperManager);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DataRetentionAssignedTo
     */
    public function createService(ServiceLocatorInterface $serviceLocator): DataRetentionAssignedTo
    {
        return $this->__invoke($serviceLocator, DataRetentionAssignedTo::class);
    }
}
