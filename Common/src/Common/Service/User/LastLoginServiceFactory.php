<?php
namespace Common\Service\User;

use Common\Service\Cqrs\Command\CommandSender;
use Laminas\ServiceManager\ServiceLocatorInterface;

final class LastLoginServiceFactory implements \Laminas\ServiceManager\FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var CommandSender $commandSender */
        $commandSender = $serviceLocator->get('CommandSender');

        return new LastLoginService($commandSender);
    }
}
