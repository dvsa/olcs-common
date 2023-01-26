<?php

namespace Common\Service\Qa\Custom\CertRoadworthiness;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class MotExpiryDateFieldsetPopulatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MotExpiryDateFieldsetPopulator
    {
        return new MotExpiryDateFieldsetPopulator(
            $container->get('Helper\Translation'),
            $container->get('QaCommonHtmlAdder'),
            $container->get('QaCommonFileUploadFieldsetGenerator')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): MotExpiryDateFieldsetPopulator
    {
        return $this->__invoke($serviceLocator, MotExpiryDateFieldsetPopulator::class);
    }
}
