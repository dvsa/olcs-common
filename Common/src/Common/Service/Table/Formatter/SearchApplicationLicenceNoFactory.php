<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SearchApplicationLicenceNoFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchApplicationLicenceNo
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchApplicationLicenceNo
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        return new SearchApplicationLicenceNo($urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SearchApplicationLicenceNo
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SearchApplicationLicenceNo
    {
        return $this->__invoke($serviceLocator, SearchApplicationLicenceNo::class);
    }
}
