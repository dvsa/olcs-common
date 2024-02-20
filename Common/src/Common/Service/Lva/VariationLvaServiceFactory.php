<?php

namespace Common\Service\Lva;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
}
