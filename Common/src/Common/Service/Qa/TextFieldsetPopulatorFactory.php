<?php

namespace Common\Service\Qa;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TextFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TextFieldsetPopulator
    {
        return new TextFieldsetPopulator(
            $container->get('QaTextFactory'),
            $container->get('QaTranslateableTextHandler')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TextFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, TextFieldsetPopulator::class);
    }
}
