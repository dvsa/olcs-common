<?php

namespace Common\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FeaturesEnabledForMethodFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FeaturesEnabledForMethod
    {
        return new FeaturesEnabledForMethod(
            $container->getServiceLocator()->get('QuerySender')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FeaturesEnabledForMethod
    {
        return $this->__invoke($serviceLocator, FeaturesEnabledForMethod::class);
    }
}
