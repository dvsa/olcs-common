<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class NoOfPermitsBothStrategySelectingFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NoOfPermitsStrategySelectingFieldsetPopulator
    {
        return new NoOfPermitsStrategySelectingFieldsetPopulator(
            $container->get('QaEcmtNoOfPermitsSingleFieldsetPopulator'),
            $container->get('QaEcmtNoOfPermitsBothFieldsetPopulator')
        );
    }
}
