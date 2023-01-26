<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsBaseInsetTextGeneratorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NoOfPermitsBaseInsetTextGenerator
    {
        return new NoOfPermitsBaseInsetTextGenerator(
            $container->get('Helper\Translation'),
            $container->get('ViewHelperManager')->get('currencyFormatter')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): NoOfPermitsBaseInsetTextGenerator
    {
        return $this->__invoke($serviceLocator, NoOfPermitsBaseInsetTextGenerator::class);
    }
}
