<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NiWarningConditionalAdderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NiWarningConditionalAdder
    {
        return new NiWarningConditionalAdder(
            $container->get('QaCommonWarningAdder')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): NiWarningConditionalAdder
    {
        return $this->__invoke($serviceLocator, NiWarningConditionalAdder::class);
    }
}
