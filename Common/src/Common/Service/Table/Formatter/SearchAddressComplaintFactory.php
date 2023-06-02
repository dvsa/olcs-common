<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SearchAddressComplaintFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchAddressComplaint
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchAddressComplaint
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        return new SearchAddressComplaint($urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SearchAddressComplaint
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SearchAddressComplaint
    {
        return $this->__invoke($serviceLocator, SearchAddressComplaint::class);
    }
}
