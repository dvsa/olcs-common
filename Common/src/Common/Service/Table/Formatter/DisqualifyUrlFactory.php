<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DisqualifyUrlFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return DisqualifyUrl
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        $router = $container->get('Router');
        $request = $container->get('Request');
        return new DisqualifyUrl($urlHelper, $router, $request);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DisqualifyUrl
     */
    public function createService(ServiceLocatorInterface $serviceLocator): DisqualifyUrl
    {
        return $this->__invoke($serviceLocator, DisqualifyUrl::class);
    }
}
