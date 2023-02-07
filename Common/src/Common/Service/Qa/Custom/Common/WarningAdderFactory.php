<?php

namespace Common\Service\Qa\Custom\Common;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class WarningAdderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): WarningAdder
    {
        return new WarningAdder(
            $container->get('ViewHelperManager')->get('partial'),
            $container->get('QaCommonHtmlAdder')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): WarningAdder
    {
        return $this->__invoke($serviceLocator, null, null);
    }
}
