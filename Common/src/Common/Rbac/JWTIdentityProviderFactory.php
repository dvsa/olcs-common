<?php

namespace Common\Rbac;

use Common\Auth\Service\RefreshTokenService;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Exception;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Session\Container;
use RuntimeException;

/**
 * @see JWTIdentityProvider
 */
class JWTIdentityProviderFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return JWTIdentityProvider
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): JWTIdentityProvider
    {
        $sessionName = $container->get('config')['auth']['session_name'] ?? '';
        if (empty($sessionName)) {
            throw new RunTimeException("Missing auth.session_name from config");
        }

        return new JWTIdentityProvider(
            new Container($sessionName),
            $container->get('QuerySender'),
            $container->get(CacheEncryption::class),
            $container->get(RefreshTokenService::class)
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     * @throws Exception
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): JWTIdentityProvider
    {
        return $this->__invoke($serviceLocator, null);
    }
}
