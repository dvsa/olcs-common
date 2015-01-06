<?php

/**
 * Abstract Lva Adapter Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Factories;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Common\Controller\Lva\AbstractController;

/**
 * Abstract Lva Adapter Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractLvaAdapterFactory implements FactoryInterface
{
    protected $adapter;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceClass = 'Common\Controller\Lva\Adapters\\' . $this->adapter;

        if (!class_exists($serviceClass)) {
            throw new \RuntimeException('Lva adapter not found: ' . $this->adapter);
        }

        return new $serviceClass();
    }
}
