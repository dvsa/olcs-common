<?php

namespace Common\Service\Data;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * RefDataServicesFactory
 */
class RefDataServicesFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return RefDataServices
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RefDataServices
    {
        return new RefDataServices(
            $container->get(AbstractListDataServiceServices::class),
            $container->get('LanguagePreference')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return RefDataServices
     */
    public function createService(ServiceLocatorInterface $services): RefDataServices
    {
        return $this($services, RefDataServices::class);
    }
}
