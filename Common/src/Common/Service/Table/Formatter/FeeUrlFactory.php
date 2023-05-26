<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class FeeUrlFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return FeeUrl
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FeeUrl
    {
        $router     = $container->get('router');
        $request    = $container->get('request');
        $urlHelper  = $container->get('Helper\Url');

        return new FeeUrl($router, $request, $urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FeeUrl
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FeeUrl
    {
        return $this->__invoke($serviceLocator, FeeUrl::class);
    }
}
