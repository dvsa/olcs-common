<?php

namespace Common\Rbac;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use RuntimeException;
use LmcRbacMvc\Identity\IdentityProviderInterface;

class IdentityProviderFactory implements FactoryInterface
{
    public const MESSAGE_CONFIG_MISSING = 'Missing auth.identity_provider from config';
    public const MESSAGE_UNABLE_TO_CREATE = 'Unable to create requested identity provider';
    public const MESSAGE_DOES_NOT_IMPLEMENT = 'Requested Identity Provider does not implement: ' . IdentityProviderInterface::class;

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return IdentityProviderInterface
     * @throws RunTimeException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IdentityProviderInterface
    {
        $identityProvider = $container->get('config')['auth']['identity_provider'] ?? '';
        if (empty($identityProvider)) {
            throw new RunTimeException(static::MESSAGE_CONFIG_MISSING);
        }

        if (!$container->has($identityProvider)) {
            throw new RunTimeException(static::MESSAGE_UNABLE_TO_CREATE);
        }

        $instance = $container->get($identityProvider);

        if (!$instance instanceof IdentityProviderInterface) {
            throw new RunTimeException(static::MESSAGE_DOES_NOT_IMPLEMENT);
        }
        return $instance;
    }
}
