<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FeeTransactionDateFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return FeeTransactionDate
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dateFormatter = $container->get(Date::class);
        $stackValueFormatter = $container->get(StackValue::class);
        return new FeeTransactionDate($dateFormatter, $stackValueFormatter);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FeeTransactionDate
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FeeTransactionDate
    {
        return $this->__invoke($serviceLocator, FeeTransactionDate::class);
    }
}
