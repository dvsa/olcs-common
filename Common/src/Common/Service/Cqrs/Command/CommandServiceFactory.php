<?php

/**
 * Command Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Command;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client;

/**
 * Command Service Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommandServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $router = $serviceLocator->get('ApiRouter');
        $client = new Client();
        $client->setOptions(
            [
                'timeout' => 60
            ]
        );
        $request = $serviceLocator->get('CqrsRequest');

        return new CommandService($router, $client, $request);
    }
}
