<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class YesNoWithMarkupForNoPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return YesNoWithMarkupForNoPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new YesNoWithMarkupForNoPopulator(
            $serviceLocator->get('QaRadioFactory'),
            $serviceLocator->get('QaBilateralYesNoRadioOptionsApplier'),
            $serviceLocator->get('QaCommonHtmlAdder')
        );
    }
}
