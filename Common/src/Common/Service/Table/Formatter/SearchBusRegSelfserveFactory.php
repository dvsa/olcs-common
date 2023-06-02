<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SearchBusRegSelfserveFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchBusRegSelfserve
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchBusRegSelfserve
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        $translator = $container->get('translator');
        return new SearchBusRegSelfserve($urlHelper, $translator);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SearchBusRegSelfserve
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SearchBusRegSelfserve
    {
        return $this->__invoke($serviceLocator, SearchBusRegSelfserve::class);
    }
}
