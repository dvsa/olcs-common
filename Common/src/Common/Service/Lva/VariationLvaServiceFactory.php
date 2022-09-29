<?php

namespace Common\Service\Lva;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * VariationLvaServiceFactory
 */
class VariationLvaServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return VariationLvaService
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): VariationLvaService
    {
        return new VariationLvaService(
            $container->get('Helper\Translation'),
            $container->get('Helper\Guidance'),
            $container->get('Helper\Url')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return VariationLvaService
     */
    public function createService(ServiceLocatorInterface $services): VariationLvaService
    {
        return $this($services, VariationLvaService::class);
    }
}
