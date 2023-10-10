<?php

namespace Common\Controller\Lva\Factories\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FormRewriteControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return FormRewriteController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormRewriteController
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        //ToDo: Migrate SM calls here
        return new FormRewriteController();
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FormRewriteController
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FormRewriteController
    {
        return $this->__invoke($serviceLocator, FormRewriteController::class);
    }
}