<?php

declare(strict_types=1);

namespace Common\Controller\Lva\Factories\Adapter;

use Common\Controller\Lva\Adapters\VariationPeopleAdapter;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class VariationPeopleAdapterFactory implements FactoryInterface
{
    /**
     * @deprecated Laminas 2 compatibility. To be removed after Laminas 3 upgrade.
     */
    public function createService(ServiceLocatorInterface $serviceLocator): VariationPeopleAdapter
    {
        $container = method_exists($serviceLocator, 'getServiceLocator') ? $serviceLocator->getServiceLocator() : $serviceLocator;

        return $this->__invoke($container, VariationPeopleAdapter::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): VariationPeopleAdapter
    {
        return new VariationPeopleAdapter($container);
    }
}
