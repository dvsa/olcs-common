<?php
declare(strict_types=1);

namespace Common\Auth\Adapter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class CommandAdapterFactory
 * @see CommandAdapter
 */
class CommandAdapterFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CommandAdapter
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CommandAdapter
    {
        $commandSender = $container->get('CommandSender');
        return new CommandAdapter($commandSender);
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, null, null);
    }
}
