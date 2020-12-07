<?php

/**
 * Command Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Command;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Http\Client;

/**
 * Command Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommandServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return CommandService
     */
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

        return new CommandService(
            $serviceLocator->get('ApiRouter'),
            $client,
            $serviceLocator->get('CqrsRequest'),
            isset($config['debug']['showApiMessages']) && $config['debug']['showApiMessages'] ? true : false,
            $serviceLocator->get('Helper\FlashMessenger')
        );
    }
}
