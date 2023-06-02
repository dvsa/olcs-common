<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class FeeAmountSumFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return FeeAmountSum
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sumFormatter = $container->get(Sum::class);
        $feeAmountFormatter = $container->get(FeeAmount::class);
        return new FeeAmountSum($sumFormatter, $feeAmountFormatter);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FeeAmountSum
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FeeAmountSum
    {
        return $this->__invoke($serviceLocator, FeeAmountSum::class);
    }
}