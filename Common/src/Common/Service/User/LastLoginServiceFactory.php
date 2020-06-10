<?php
namespace Common\Service\User;

use Common\Service\Cqrs\Command\CommandSender;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;

final class LastLoginServiceFactory implements ServiceLocatorAwareInterface, \Zend\ServiceManager\FactoryInterface
{
    use ServiceLocatorAwareTrait;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var CommandSender $commandSender */
        $commandSender = $serviceLocator->get('CommandSender');

        return new LastLoginService($commandSender);
    }
}
