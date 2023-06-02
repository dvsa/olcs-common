<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SearchAddressOperatorNameFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchAddressOperatorName
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchAddressOperatorName
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        return new SearchAddressOperatorName($urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SearchAddressOperatorName
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SearchAddressOperatorName
    {
        return $this->__invoke($serviceLocator, SearchAddressOperatorName::class);
    }
}
