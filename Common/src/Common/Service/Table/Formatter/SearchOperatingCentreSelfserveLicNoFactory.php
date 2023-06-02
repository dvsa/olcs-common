<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SearchOperatingCentreSelfserveLicNoFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchOperatingCentreSelfserveLicNo
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchOperatingCentreSelfserveLicNo
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $translator = $container->get('translator');
        return new SearchOperatingCentreSelfserveLicNo($translator);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SearchOperatingCentreSelfserveLicNo
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SearchOperatingCentreSelfserveLicNo
    {
        return $this->__invoke($serviceLocator, SearchOperatingCentreSelfserveLicNo::class);
    }
}
