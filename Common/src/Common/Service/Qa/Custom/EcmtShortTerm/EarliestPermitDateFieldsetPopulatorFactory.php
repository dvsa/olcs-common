<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EarliestPermitDateFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EarliestPermitDateFieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new EarliestPermitDateFieldsetPopulator(
            $serviceLocator->get('Helper\Translation'),
            $serviceLocator->get('QaCommonHtmlAdder')
        );
    }
}
