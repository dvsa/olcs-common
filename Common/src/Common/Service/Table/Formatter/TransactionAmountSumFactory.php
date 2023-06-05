<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class TransactionAmountSumFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return TransactionAmountSum
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        $formatterPluginManager = $container->get(FormatterPluginManager::class);
        $moneyFormatter = $formatterPluginManager->get(Money::class);
        return new TransactionAmountSum($moneyFormatter);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TransactionAmountSum
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TransactionAmountSum
    {
        return $this->__invoke($serviceLocator, TransactionAmountSum::class);
    }
}
