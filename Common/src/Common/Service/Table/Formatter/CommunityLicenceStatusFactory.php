<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CommunityLicenceStatusFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return CommunityLicenceStatus
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        $router = $container->get('router');
        $request = $container->get('request');
        return new CommunityLicenceStatus($urlHelper, $router, $request);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CommunityLicenceStatus
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CommunityLicenceStatus
    {
        return $this->__invoke($serviceLocator, CommunityLicenceStatus::class);
    }
}
