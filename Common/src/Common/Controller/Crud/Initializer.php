<?php

/**
 * Crud Initializer
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Crud;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Common\Controller\Interfaces\CrudControllerInterface;

/**
 * Crud Initializer
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Initializer implements InitializerInterface
{
    /**
     * Initialize
     *
     * @param $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        // Ignore non-crud controllers
        if (! ($instance instanceof CrudControllerInterface)) {
            return;
        }

        $listener = $serviceLocator->getServiceLocator()->get('CrudListener');
        $listener->setController($instance);

        $em = $instance->getEventManager();
        $em->attach($listener);
    }
}
