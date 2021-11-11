<?php
declare(strict_types=1);

namespace Common\Auth\Listener;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Session\Container;
use RuntimeException;

class RefreshJWTListenerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return RefreshJWTListener
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RefreshJWTListener
    {
        $sessionName = $container->get('config')['auth']['session_name'] ?? '';
        if (empty($sessionName)) {
            throw new RunTimeException("Missing auth.session_name from config");
        }

        return new RefreshJWTListener(
            $container->get('CommandSender'),
            new Container($sessionName),
        );
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return RefreshJWTListener
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): RefreshJWTListener
    {
        return $this->__invoke($serviceLocator, null);
    }
}
