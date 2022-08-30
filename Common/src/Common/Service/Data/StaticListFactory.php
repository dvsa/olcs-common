<?php

namespace Common\Service\Data;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * StaticListFactory
 */
class StaticListFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return StaticList
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StaticList
    {
        return new StaticList(
            $container->get(AbstractDataServiceServices::class),
            $container->get('Config')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return StaticList
     */
    public function createService(ServiceLocatorInterface $services): StaticList
    {
        return $this($services, StaticList::class);
    }
}
