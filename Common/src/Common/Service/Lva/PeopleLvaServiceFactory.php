<?php

namespace Common\Service\Lva;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
}
