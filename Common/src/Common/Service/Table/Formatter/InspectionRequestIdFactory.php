<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class InspectionRequestIdFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return InspectionRequestId
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        $router = $container->get('router');
        $request = $container->get('request');
        return new InspectionRequestId($urlHelper, $router, $request);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return InspectionRequestId
     */
    public function createService(ServiceLocatorInterface $serviceLocator): InspectionRequestId
    {
        return $this->__invoke($serviceLocator, InspectionRequestId::class);
    }
}
