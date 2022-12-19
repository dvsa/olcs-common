<?php

namespace Common\Service\Qa;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FormattedTranslateableTextParametersGeneratorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormattedTranslateableTextParametersGenerator
    {
        return new FormattedTranslateableTextParametersGenerator(
            $container->get('QaTranslateableTextParameterHandler')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FormattedTranslateableTextParametersGenerator
    {
        return $this->__invoke($serviceLocator, FormattedTranslateableTextParametersGenerator::class);
    }
}
