<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NoOfPermitsBothFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NoOfPermitsBothFieldsetPopulator
    {
        return new NoOfPermitsBothFieldsetPopulator(
            $container->get('Helper\Translation'),
            $container->get('QaEcmtNoOfPermitsBaseInsetTextGenerator'),
            $container->get('QaCommonHtmlAdder')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): NoOfPermitsBothFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, NoOfPermitsBothFieldsetPopulator::class);
    }
}
