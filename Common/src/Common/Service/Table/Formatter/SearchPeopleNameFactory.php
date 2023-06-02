<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SearchPeopleNameFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchPeopleName
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchPeopleName
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        return new SearchPeopleName($urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SearchPeopleName
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SearchPeopleName
    {
        return $this->__invoke($serviceLocator, SearchPeopleName::class);
    }
}
