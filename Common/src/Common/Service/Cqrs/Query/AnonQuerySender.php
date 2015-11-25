<?php

namespace Common\Service\Cqrs\Query;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Anon Query Sender
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @todo Remove this class when we are fully integrated with OpenAM
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
