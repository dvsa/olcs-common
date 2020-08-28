<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsEitherStrategySelectingFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsStrategySelectingFieldsetPopulator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NoOfPermitsStrategySelectingFieldsetPopulator(
            $serviceLocator->get('QaEcmtNoOfPermitsSingleFieldsetPopulator'),
            $serviceLocator->get('QaEcmtNoOfPermitsEitherFieldsetPopulator')
        );
    }
}
