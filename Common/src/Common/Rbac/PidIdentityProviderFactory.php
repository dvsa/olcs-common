<?php

/**
 * Identity Provider Factory
 */
namespace Common\Rbac;

use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Session\Container;

/**
 * Pid Identity Provider Factory
 */
class PidIdentityProviderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PidIdentityProvider
    {
        return new PidIdentityProvider(
            $container->get('QuerySender'),
            new Container('user_details'),
            $container->get('Request'),
            $container->get(CacheEncryption::class)
        );
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PidIdentityProvider
    {
        return $this->__invoke($serviceLocator, PidIdentityProvider::class);
    }
}
