<?php

namespace Common\Controller\Lva\Factories\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FileControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return FileController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FileController
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        //ToDo: Migrate SM calls here
        return new FileController();
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FileController
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FileController
    {
        return $this->__invoke($serviceLocator, FileController::class);
    }
}