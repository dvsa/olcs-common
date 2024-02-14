<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class SectorsFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SectorsFieldsetPopulator
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SectorsFieldsetPopulator
    {
        return new SectorsFieldsetPopulator(
            $container->get('Helper\Translation'),
            $container->get('QaRadioFieldsetPopulator')
        );
    }
}
