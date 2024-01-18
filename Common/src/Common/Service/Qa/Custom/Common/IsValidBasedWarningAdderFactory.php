<?php

namespace Common\Service\Qa\Custom\Common;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IsValidBasedWarningAdderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IsValidBasedWarningAdder
    {
        return new IsValidBasedWarningAdder(
            $container->get('QaCommonWarningAdder')
        );
    }
}
