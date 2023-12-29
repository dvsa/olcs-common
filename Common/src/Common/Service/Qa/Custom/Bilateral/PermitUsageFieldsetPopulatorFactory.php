<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PermitUsageFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PermitUsageFieldsetPopulator
    {
        return new PermitUsageFieldsetPopulator(
            $container->get('QaRadioFieldsetPopulator'),
            $container->get('Helper\Translation'),
            $container->get('QaCommonHtmlAdder')
        );
    }
}
