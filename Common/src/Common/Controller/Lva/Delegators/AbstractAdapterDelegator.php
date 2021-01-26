<?php

/**
 * Abstract Adapter Delegator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Delegators;

use Common\Controller\Lva\Interfaces\ControllerAwareInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\DelegatorFactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract Adapter Delegator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractAdapterDelegator implements DelegatorFactoryInterface
{
    protected $adapter;

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, callable $callback, array $options = null)
    {
        $controller = $callback();

        $adapter = $container->getServiceLocator()->get($this->adapter);

        if ($adapter instanceof ControllerAwareInterface) {
            $adapter->setController($controller);
        }

        $controller->setAdapter($adapter);

        return $controller;
    }

    /**
     * {@inheritdoc}
     * @todo OLCS-28149
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        return $this($serviceLocator, $requestedName, $callback);
    }
}
