<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class NoOfPermitsEitherFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NoOfPermitsEitherFieldsetPopulator
    {
        return new NoOfPermitsEitherFieldsetPopulator(
            $container->get('Helper\Translation'),
            $container->get('QaEcmtNoOfPermitsBaseInsetTextGenerator'),
            $container->get('QaCommonHtmlAdder')
        );
    }
}
