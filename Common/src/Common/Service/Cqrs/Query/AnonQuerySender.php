<?php

namespace Common\Service\Cqrs\Query;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Anon Query Sender
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AnonQuerySender extends QuerySender
{
    /**
     * Grab the appropriate query service from the service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return QueryService
     */
    protected function getQueryService(ServiceLocatorInterface $serviceLocator)
    {
        return $serviceLocator->get('AnonQueryService');
    }
}
