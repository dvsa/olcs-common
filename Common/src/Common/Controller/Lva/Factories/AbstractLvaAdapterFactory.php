<?php

/**
 * Abstract Lva Adapter Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Factories;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract Lva Adapter Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractLvaAdapterFactory implements FactoryInterface
{
    protected $adapter;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $serviceClass = 'Common\Controller\Lva\Adapters\\' . $this->adapter;

        return new $serviceClass();
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, null);
    }
}
