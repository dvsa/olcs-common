<?php

namespace Common\Service\Qa;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class RadioFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RadioFieldsetPopulator
    {
        return new RadioFieldsetPopulator(
            $container->get('QaRadioFactory'),
            $container->get('QaTranslateableTextHandler')
        );
    }
}
