<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TransactionFeeStatusFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return TransactionFeeStatus
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $router = $container->get('router');
        $request = $container->get('request');
        $urlHelper = $container->get('Helper\Url');

        return new TransactionFeeStatus($router, $request, $urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TransactionFeeStatus
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TransactionFeeStatus
    {
        return $this->__invoke($serviceLocator, TransactionFeeStatus::class);
    }
}
