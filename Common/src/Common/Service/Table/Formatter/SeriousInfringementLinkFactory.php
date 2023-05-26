<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SeriousInfringementLinkFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SeriousInfringementLink
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        return new SeriousInfringementLink($urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SeriousInfringementLink
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SeriousInfringementLink
    {
        return $this->__invoke($serviceLocator, SeriousInfringementLink::class);
    }
}
