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
 * @todo Remove this class when we are fully integrated with OpenAM
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
