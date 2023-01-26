<?php

namespace Common\Service\Qa;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CheckboxFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CheckboxFieldsetPopulator
    {
        return new CheckboxFieldsetPopulator(
            $container->get('QaCheckboxFactory'),
            $container->get('QaTranslateableTextHandler')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CheckboxFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, CheckboxFieldsetPopulator::class);
    }
}
