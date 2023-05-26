<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class FeeNoAndStatusFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return FeeNoAndStatus
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $feeStatusFormatter = $container->get(FeeStatus::class);
        return new FeeNoAndStatus($feeStatusFormatter);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FeeNoAndStatus
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FeeNoAndStatus
    {
        return $this->__invoke($serviceLocator, FeeNoAndStatus::class);
    }
}
