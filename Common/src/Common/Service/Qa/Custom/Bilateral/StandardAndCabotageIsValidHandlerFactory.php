<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class StandardAndCabotageIsValidHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StandardAndCabotageIsValidHandler
    {
        return new StandardAndCabotageIsValidHandler(
            $container->get('QaBilateralStandardAndCabotageSubmittedAnswerGenerator')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): StandardAndCabotageIsValidHandler
    {
        return $this->__invoke($serviceLocator, StandardAndCabotageIsValidHandler::class);
    }
}
