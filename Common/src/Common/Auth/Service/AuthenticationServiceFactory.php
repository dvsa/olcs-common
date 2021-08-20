<?php
declare(strict_types = 1);

namespace Common\Auth\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
        return new AuthenticationService();
    }

    /**
     * @inheritDoc
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): AuthenticationService
    {
        return $this->__invoke($serviceLocator, null, null);
    }
}
