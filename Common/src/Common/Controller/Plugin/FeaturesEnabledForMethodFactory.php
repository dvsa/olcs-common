<?php

namespace Common\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FeaturesEnabledForMethodFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FeaturesEnabledForMethod
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        return new FeaturesEnabledForMethod(
            $container->get('QuerySender')
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
