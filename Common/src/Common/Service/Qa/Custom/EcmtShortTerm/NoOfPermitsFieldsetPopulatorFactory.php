<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsFieldsetPopulatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoOfPermitsFieldsetPopulatorFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new NoOfPermitsFieldsetPopulator(
            $serviceLocator->get('Helper\Translation')
        );
    }
}
