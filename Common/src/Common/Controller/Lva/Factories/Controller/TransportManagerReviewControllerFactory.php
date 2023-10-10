<?php

namespace Common\Controller\Lva\Factories\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TransportManagerReviewControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return TransportManagerReviewController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TransportManagerReviewController
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        //ToDo: Migrate SM calls here
        return new TransportManagerReviewController();
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TransportManagerReviewController
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TransportManagerReviewController
    {
        return $this->__invoke($serviceLocator, TransportManagerReviewController::class);
    }
}