<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class AnnualTripsAbroadDataHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AnnualTripsAbroadDataHandler
    {
        return new AnnualTripsAbroadDataHandler(
            $container->get('QaCommonIsValidBasedWarningAdder'),
            $container->get('QaEcmtAnnualTripsAbroadIsValidHandler')
        );
    }


    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): AnnualTripsAbroadDataHandler
    {
        return $this->__invoke($serviceLocator, AnnualTripsAbroadDataHandler::class);
    }
}
