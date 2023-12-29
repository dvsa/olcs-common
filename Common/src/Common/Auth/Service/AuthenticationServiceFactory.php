<?php
declare(strict_types = 1);

namespace Common\Auth\Service;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\Storage\Session;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return AuthenticationService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AuthenticationService
    {
        $instance = new AuthenticationService();
        $instance->setStorage($container->get(Session::class));

        return $instance;
    }
}
