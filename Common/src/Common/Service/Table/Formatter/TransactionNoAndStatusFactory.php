<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class TransactionNoAndStatusFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return TransactionNoAndStatus
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        $formatterPluginManager = $container->get(FormatterPluginManager::class);
        $transactionUrlFormatter = $formatterPluginManager->get(TransactionUrl::class);
        $trasactionStatusFormatter = $formatterPluginManager->get(TransactionStatus::class);
        return new TransactionNoAndStatus($transactionUrlFormatter, $trasactionStatusFormatter);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TransactionNoAndStatus
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TransactionNoAndStatus
    {
        return $this->__invoke($serviceLocator, TransactionNoAndStatus::class);
    }
}
