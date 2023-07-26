<?php

declare(strict_types=1);

namespace Common\Controller\Lva\Factories\Adapter;

use Common\Controller\Lva\Adapters\LicenceLvaAdapter;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class LicenceLvaAdapterFactory implements FactoryInterface
{
    /**
     * @deprecated Laminas 2 compatibility. To be removed after Laminas 3 upgrade.
     */
    public function createService(ServiceLocatorInterface $serviceLocator): LicenceLvaAdapter
    {
        $container = method_exists($serviceLocator, 'getServiceLocator') ? $serviceLocator->getServiceLocator() : $serviceLocator;

        return $this->__invoke($container, LicenceLvaAdapter::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LicenceLvaAdapter
    {
        return new LicenceLvaAdapter($container);
    }
}
