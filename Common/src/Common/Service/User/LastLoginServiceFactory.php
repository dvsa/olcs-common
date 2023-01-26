<?php
namespace Common\Service\User;

use Common\Service\Cqrs\Command\CommandSender;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

final class LastLoginServiceFactory implements \Laminas\ServiceManager\FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LastLoginService
    {
        /** @var CommandSender $commandSender */
        $commandSender = $container->get('CommandSender');

        return new LastLoginService($commandSender);
    }

    public function createService(ServiceLocatorInterface $serviceLocator): LastLoginService
    {
        return $this->__invoke($serviceLocator, LastLoginService::class);
    }
}
