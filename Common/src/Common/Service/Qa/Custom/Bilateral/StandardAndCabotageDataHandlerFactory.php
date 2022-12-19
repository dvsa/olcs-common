<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class StandardAndCabotageDataHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StandardAndCabotageDataHandler
    {
        return new StandardAndCabotageDataHandler(
            $container->get('QaBilateralStandardAndCabotageSubmittedAnswerGenerator'),
            $container->get('QaBilateralStandardAndCabotageIsValidHandler'),
            $container->get('QaCommonWarningAdder')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): StandardAndCabotageDataHandler
    {
        return $this->__invoke($serviceLocator, StandardAndCabotageDataHandler::class);
    }
}
