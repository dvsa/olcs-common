<?php

namespace Common\Service\Data;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class VenueFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return Venue
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Venue
    {
        return new Venue(
            $container->get('DataServiceManager')->get(AbstractDataServiceServices::class),
            $container->get('DataServiceManager')->get(Licence::class)
        );
    }
}
