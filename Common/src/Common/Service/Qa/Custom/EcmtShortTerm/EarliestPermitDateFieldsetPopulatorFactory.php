<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EarliestPermitDateFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EarliestPermitDateFieldsetPopulator
    {
        return new EarliestPermitDateFieldsetPopulator(
            $container->get('Helper\Translation'),
            $container->get('QaCommonHtmlAdder')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): EarliestPermitDateFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, EarliestPermitDateFieldsetPopulator::class);
    }
}
