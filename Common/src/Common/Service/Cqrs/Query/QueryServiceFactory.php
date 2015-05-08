<?php

/**
 * Query Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Query;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client;

/**
 * Query Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $router = $serviceLocator->get('ApiRouter');
        $client = new Client();

        return new QueryService($router, $client);
    }
}
