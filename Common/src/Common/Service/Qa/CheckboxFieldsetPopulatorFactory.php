<?php

namespace Common\Service\Qa;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CheckboxFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CheckboxFieldsetPopulator
    {
        return new CheckboxFieldsetPopulator(
            $container->get('QaCheckboxFactory'),
            $container->get('QaTranslateableTextHandler')
        );
    }
}
