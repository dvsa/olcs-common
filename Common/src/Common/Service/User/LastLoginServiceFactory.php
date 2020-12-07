<?php
namespace Common\Service\User;

use Common\Service\Cqrs\Command\CommandSender;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorAwareTrait;
use Laminas\ServiceManager\ServiceLocatorInterface;

final class LastLoginServiceFactory implements ServiceLocatorAwareInterface, \Laminas\ServiceManager\FactoryInterface
{
    use ServiceLocatorAwareTrait;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var CommandSender $commandSender */
        $commandSender = $serviceLocator->get('CommandSender');

        return new LastLoginService($commandSender);
    }
}
