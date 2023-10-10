<?php

namespace Common\Controller\Lva\Factories\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ErrorControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ErrorController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ErrorController
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        //ToDo: Migrate SM calls here
        return new ErrorController();
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ErrorController
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ErrorController
    {
        return $this->__invoke($serviceLocator, ErrorController::class);
    }
}