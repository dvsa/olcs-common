<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TaskDescriptionFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return TaskDescription
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $router = $container->get('router');
        $request = $container->get('request');
        $urlHelper = $container->get('Helper\Url');
        return new TaskDescription($router, $request, $urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TaskDescription
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TaskDescription
    {
        return $this->__invoke($serviceLocator, TaskDescription::class);
    }
}
