<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TransportManagerNameFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return TransportManagerName
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $translator = $container->get('translator');
        $urlHelper = $container->get('Helper\Url');

        return new TransportManagerName($urlHelper, $translator);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TransportManagerName
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TransportManagerName
    {
        return $this->__invoke($serviceLocator, TransportManagerName::class);
    }
}
