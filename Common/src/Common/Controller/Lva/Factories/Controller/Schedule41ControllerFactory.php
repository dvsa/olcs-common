<?php

namespace Common\Controller\Lva\Factories\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class Schedule41ControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Schedule41Controller
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Schedule41Controller
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        //ToDo: Migrate SM calls here
        return new Schedule41Controller();
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Schedule41Controller
     */
    public function createService(ServiceLocatorInterface $serviceLocator): Schedule41Controller
    {
        return $this->__invoke($serviceLocator, Schedule41Controller::class);
    }
}