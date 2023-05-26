<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FeeIdUrlFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return FeeIdUrl
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        $request = $container->get('Request');
        $router = $container->get('Router');
        return new FeeIdUrl($router, $request, $urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FeeIdUrl
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FeeIdUrl
    {
        return $this->__invoke($serviceLocator, FeeIdUrl::class);
    }
}
