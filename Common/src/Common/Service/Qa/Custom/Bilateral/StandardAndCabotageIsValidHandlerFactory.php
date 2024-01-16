<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class StandardAndCabotageIsValidHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StandardAndCabotageIsValidHandler
    {
        return new StandardAndCabotageIsValidHandler(
            $container->get('QaBilateralStandardAndCabotageSubmittedAnswerGenerator')
        );
    }
}
