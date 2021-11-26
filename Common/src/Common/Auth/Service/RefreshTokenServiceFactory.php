<?php
declare(strict_types=1);

namespace Common\Auth\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class RefreshTokenServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return RefreshTokenService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RefreshTokenService
    {
        return new RefreshTokenService(
            $container->get('CommandSender')
        );
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return RefreshTokenService
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): RefreshTokenService
    {
        return $this->__invoke($serviceLocator, null);
    }
}
