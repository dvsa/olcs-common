<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class BusRegNumberLinkFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return BusRegNumberLink
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $translator = $container->get('translator');
        $viewHelperManager = $container->get('ViewHelperManager');
        $urlHelper = $container->get('Helper\Url');
        return new BusRegNumberLink($translator, $viewHelperManager, $urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return BusRegNumberLink
     */
    public function createService(ServiceLocatorInterface $serviceLocator): BusRegNumberLink
    {
        return $this->__invoke($serviceLocator, BusRegNumberLink::class);
    }
}
