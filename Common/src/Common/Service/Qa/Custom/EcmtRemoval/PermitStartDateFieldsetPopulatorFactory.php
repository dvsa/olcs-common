<?php

namespace Common\Service\Qa\Custom\EcmtRemoval;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class PermitStartDateFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PermitStartDateFieldsetPopulator
    {
        return new PermitStartDateFieldsetPopulator(
            $container->get('Helper\Translation'),
            $container->get('QaCommonHtmlAdder')
        );
    }

    /**
     * @dataProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PermitStartDateFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, PermitStartDateFieldsetPopulator::class);
    }
}
