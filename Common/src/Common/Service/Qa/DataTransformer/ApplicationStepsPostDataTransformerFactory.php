<?php

namespace Common\Service\Qa\DataTransformer;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApplicationStepsPostDataTransformerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ApplicationStepsPostDataTransformer
    {
        return new ApplicationStepsPostDataTransformer(
            $container->get('QaDataTransformerProvider')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ApplicationStepsPostDataTransformer
    {
        return $this->__invoke($serviceLocator, ApplicationStepsPostDataTransformer::class);
    }
}
