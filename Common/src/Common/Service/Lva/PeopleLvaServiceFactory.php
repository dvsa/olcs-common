<?php

namespace Common\Service\Lva;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * PeopleLvaServiceFactory
 */
class PeopleLvaServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return PeopleLvaService
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PeopleLvaService
    {
        return new PeopleLvaService(
            $container->get('Helper\Form')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return PeopleLvaService
     */
    public function createService(ServiceLocatorInterface $services): PeopleLvaService
    {
        return $this($services, PeopleLvaService::class);
    }
}
