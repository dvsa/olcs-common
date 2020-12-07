<?php

/**
 * Query Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Query;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Http\Client;
use Laminas\Http\Request;

/**
 * Query Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        $clientOptions = [];
        if (isset($config['cqrs_client'])) {
            $clientOptions = $config['cqrs_client'];
        }

        $client = new Client(null, $clientOptions);

        $adapter = new ClientAdapterLoggingWrapper();
        $adapter->wrapAdapter($client);

        return new QueryService(
            $serviceLocator->get('ApiRouter'),
            $client,
            $this->getRequest($serviceLocator),
            isset($config['debug']['showApiMessages']) && $config['debug']['showApiMessages'] ? true : false,
            $serviceLocator->get('Helper\FlashMessenger')
        );
    }

    /**
     * Grab the appropriate request object
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Request
     */
    protected function getRequest(ServiceLocatorInterface $serviceLocator)
    {
        return $serviceLocator->get('CqrsRequest');
    }
}
