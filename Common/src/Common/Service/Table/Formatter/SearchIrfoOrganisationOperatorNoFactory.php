<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SearchIrfoOrganisationOperatorNoFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchIrfoOrganisationOperatorNo
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchIrfoOrganisationOperatorNo
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        return new SearchIrfoOrganisationOperatorNo($urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SearchIrfoOrganisationOperatorNo
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SearchIrfoOrganisationOperatorNo
    {
        return $this->__invoke($serviceLocator, SearchIrfoOrganisationOperatorNo::class);
    }
}
