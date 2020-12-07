<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class InternationalJourneysFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return InternationalJourneysFieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new InternationalJourneysFieldsetPopulator(
            $serviceLocator->get('QaRadioFieldsetPopulator'),
            $serviceLocator->get('QaEcmtNiWarningConditionalAdder')
        );
    }
}
