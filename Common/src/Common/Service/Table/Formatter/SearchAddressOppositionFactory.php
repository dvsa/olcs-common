<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SearchAddressOppositionFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchAddressComplaint
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchAddressOpposition
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        return new SearchAddressOpposition($urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SearchAddressOpposition
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SearchAddressOpposition
    {
        return $this->__invoke($serviceLocator, SearchAddressOpposition::class);
    }
}
