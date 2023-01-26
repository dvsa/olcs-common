<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class InternationalJourneysDataHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): InternationalJourneysDataHandler
    {
        return new InternationalJourneysDataHandler(
            $container->get('QaCommonIsValidBasedWarningAdder'),
            $container->get('QaEcmtInternationalJourneysIsValidHandler')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): InternationalJourneysDataHandler
    {
        return $this->__invoke($serviceLocator, InternationalJourneysDataHandler::class);
    }
}
