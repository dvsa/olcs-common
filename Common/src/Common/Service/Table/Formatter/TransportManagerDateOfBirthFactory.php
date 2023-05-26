<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TransportManagerDateOfBirthFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return TransportManagerDateOfBirth
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $viewHelperManager = $container->get('ViewHelperManager');
        return new TransportManagerDateOfBirth($viewHelperManager);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TransportManagerDateOfBirth
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TransportManagerDateOfBirth
    {
        return $this->__invoke($serviceLocator, TransportManagerDateOfBirth::class);
    }
}
