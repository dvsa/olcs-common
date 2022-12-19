<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class SectorsFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SectorsFieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SectorsFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, SectorsFieldsetPopulator::class);
    }

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
