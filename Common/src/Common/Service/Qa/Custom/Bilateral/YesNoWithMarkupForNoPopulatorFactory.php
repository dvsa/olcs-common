<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class YesNoWithMarkupForNoPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): YesNoWithMarkupForNoPopulator
    {
        return new YesNoWithMarkupForNoPopulator(
            $container->get('QaRadioFactory'),
            $container->get('QaBilateralYesNoRadioOptionsApplier'),
            $container->get('QaCommonHtmlAdder')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, YesNoWithMarkupForNoPopulator::class);
    }
}
