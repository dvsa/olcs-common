<?php

declare(strict_types=1);

namespace Common\Controller\Lva\Factories\Adapter;

use Common\Controller\Lva\Adapters\ApplicationConditionsUndertakingsAdapter;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApplicationConditionsUndertakingsAdapterFactory implements FactoryInterface
{
    /**
     * @deprecated Laminas 2 compatibility. To be removed after Laminas 3 upgrade.
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ApplicationConditionsUndertakingsAdapter
    {
        $container = method_exists($serviceLocator, 'getServiceLocator') ? $serviceLocator->getServiceLocator() : $serviceLocator;

        return $this->__invoke($container, ApplicationConditionsUndertakingsAdapter::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ApplicationConditionsUndertakingsAdapter
    {
        return new ApplicationConditionsUndertakingsAdapter($container);
    }
}
