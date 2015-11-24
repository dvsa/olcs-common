<?php

/**
 * Anon Query Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Query;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Request;

/**
 * Anon Query Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AnonQueryServiceFactory extends QueryServiceFactory
{
    /**
     * Grab the anon request object
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Request
     */
    protected function getRequest(ServiceLocatorInterface $serviceLocator)
    {
        return $serviceLocator->get('AnonCqrsRequest');
    }
}
