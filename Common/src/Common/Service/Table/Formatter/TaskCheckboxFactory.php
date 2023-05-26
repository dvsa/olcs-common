<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TaskCheckboxFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return TaskCheckbox
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $tableBuilder = $container->get('TableBuilder');
        return new TaskCheckbox($tableBuilder);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TaskCheckbox
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TaskCheckbox
    {
        return $this->__invoke($serviceLocator, TaskCheckbox::class);
    }
}
