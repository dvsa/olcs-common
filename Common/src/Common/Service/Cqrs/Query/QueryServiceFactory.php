<?php

/**
 * Query Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Query;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Interop\Container\ContainerInterface;
use Laminas\Http\Client;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Session\Container;
use RunTimeException;

class QueryServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return QueryService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): QueryService
    {
        $container = $container->getServiceLocator();

        $config = $container->get('Config');

        $clientOptions = [];
        if (isset($config['cqrs_client'])) {
            $clientOptions = $config['cqrs_client'];
        }

        $client = new Client(null, $clientOptions);

        $adapter = new ClientAdapterLoggingWrapper();
        $adapter->wrapAdapter($client);

        $sessionName = $config['auth']['session_name'] ?? '';
        if (empty($sessionName)) {
            throw new RunTimeException("Missing auth.session_name from config");
        }

        return new QueryService(
            $container->get('ApiRouter'),
            $client,
            $container->get('CqrsRequest'),
            isset($config['debug']['showApiMessages']) && $config['debug']['showApiMessages'] ? true : false,
            $container->get('Helper\FlashMessenger'),
            new Container($sessionName)
        );
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return QueryService
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): QueryService
    {
        return $this->__invoke($serviceLocator, null);
    }
}
