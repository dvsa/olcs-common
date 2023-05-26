<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FeatureToggleEditLinkFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return FeatureToggleEditLink
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        return new FeatureToggleEditLink($urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FeatureToggleEditLink
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FeatureToggleEditLink
    {
        return $this->__invoke($serviceLocator, FeatureToggleEditLink::class);
    }
}
