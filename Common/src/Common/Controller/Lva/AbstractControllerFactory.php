<?php

namespace Common\Controller\Lva;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractFactoryInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract Controller Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractControllerFactory implements AbstractFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $config = $container->getServiceLocator()->get('Config');

        return isset($config['controllers']['lva_controllers'][$requestedName]);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ServiceLocatorInterface $sm */
        $sm = $container->getServiceLocator();

        $config = $sm->get('Config');

        $class = $config['controllers']['lva_controllers'][$requestedName];

        $controller = new $class;

        if ($controller instanceof FactoryInterface) {
            return $controller->createService($sm);
        }

        return $controller;
    }

    /**
     * {@inheritdoc}
     * @todo OLCS-28149
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->canCreate($serviceLocator, $requestedName);
    }

    /**
     * {@inheritdoc}
     * @todo OLCS-28149
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this($serviceLocator, $requestedName);
    }
}
