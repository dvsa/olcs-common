<?php

namespace Common\Service\Data;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * AbstractListDataServiceServicesFactory
 */
class AbstractListDataServiceServicesFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return AbstractListDataServiceServices
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AbstractListDataServiceServices
    {
        return new AbstractListDataServiceServices(
            $container->get(AbstractDataServiceServices::class)
        );
    }
}
