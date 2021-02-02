<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CheckEcmtNeededFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CheckEcmtNeededFieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new CheckEcmtNeededFieldsetPopulator(
            $serviceLocator->get('QaCheckboxFieldsetPopulator'),
            $serviceLocator->get('QaEcmtInfoIconAdder')
        );
    }
}
