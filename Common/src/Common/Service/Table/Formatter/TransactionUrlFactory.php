<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TransactionUrlFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return TransactionUrl
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $router = $container->get('router');
        $request = $container->get('request');
        $urlHelper = $container->get('Helper\Url');
        return new TransactionUrl($router, $request, $urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TransactionUrl
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TransactionUrl
    {
        return $this->__invoke($serviceLocator, TransactionUrl::class);
    }
}
