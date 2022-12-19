<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class StandardYesNoValueOptionsGeneratorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StandardYesNoValueOptionsGenerator
    {
        return new StandardYesNoValueOptionsGenerator(
            $container->get('QaBilateralYesNoValueOptionsGenerator')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): StandardYesNoValueOptionsGenerator
    {
        return $this->__invoke($serviceLocator, StandardYesNoValueOptionsGenerator::class);
    }
}
