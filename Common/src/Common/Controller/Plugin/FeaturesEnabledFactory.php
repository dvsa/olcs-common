<?php

namespace Common\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FeaturesEnabledFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FeaturesEnabled
    {
        return new FeaturesEnabled(
            $container->getServiceLocator()->get('QuerySender')
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FeaturesEnabled
    {
        return $this->__invoke($serviceLocator, FeaturesEnabled::class);
    }
}
