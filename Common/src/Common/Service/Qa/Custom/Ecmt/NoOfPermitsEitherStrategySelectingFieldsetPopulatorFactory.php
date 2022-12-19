<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsEitherStrategySelectingFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NoOfPermitsStrategySelectingFieldsetPopulator
    {
        return new NoOfPermitsStrategySelectingFieldsetPopulator(
            $container->get('QaEcmtNoOfPermitsSingleFieldsetPopulator'),
            $container->get('QaEcmtNoOfPermitsEitherFieldsetPopulator')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): NoOfPermitsStrategySelectingFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, NoOfPermitsStrategySelectingFieldsetPopulator::class);
    }
}
