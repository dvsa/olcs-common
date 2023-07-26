<?php

declare(strict_types=1);

namespace Common\Controller\Lva\Factories\Adapter;

use Common\Controller\Lva\Adapters\VariationConditionsUndertakingsAdapter;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class VariationConditionsUndertakingsAdapterFactory implements FactoryInterface
{
    /**
     * @deprecated Laminas 2 compatibility. To be removed after Laminas 3 upgrade.
     */
    public function createService(ServiceLocatorInterface $serviceLocator): VariationConditionsUndertakingsAdapter
    {
        $container = method_exists($serviceLocator, 'getServiceLocator') ? $serviceLocator->getServiceLocator() : $serviceLocator;

        return $this->__invoke($container, VariationConditionsUndertakingsAdapter::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): VariationConditionsUndertakingsAdapter
    {
        return new VariationConditionsUndertakingsAdapter($container);
    }
}
