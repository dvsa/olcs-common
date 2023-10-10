<?php

namespace Common\Controller\Lva\Factories\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class GuidesControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return GuidesController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GuidesController
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        //ToDo: Migrate SM calls here
        return new GuidesController();
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GuidesController
     */
    public function createService(ServiceLocatorInterface $serviceLocator): GuidesController
    {
        return $this->__invoke($serviceLocator, GuidesController::class);
    }
}