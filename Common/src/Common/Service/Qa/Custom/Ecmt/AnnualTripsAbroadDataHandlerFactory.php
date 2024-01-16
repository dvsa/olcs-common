<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AnnualTripsAbroadDataHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AnnualTripsAbroadDataHandler
    {
        return new AnnualTripsAbroadDataHandler(
            $container->get('QaCommonIsValidBasedWarningAdder'),
            $container->get('QaEcmtAnnualTripsAbroadIsValidHandler')
        );
    }
}
