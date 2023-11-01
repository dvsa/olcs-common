<?php

namespace Common\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use RuntimeException;
use LmcRbacMvc\Service\AuthorizationService;

class CurrentUserFactory implements FactoryInterface
{
    public const MSG_MISSING_ANALYTICS_CONFIG = 'Missing auth.user_unique_id_salt from config';

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return CurrentUser
     * @throws RuntimeException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CurrentUser
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        $config = $container->get('Config');

        if (!isset($config['auth']['user_unique_id_salt'])) {
            throw new RunTimeException(self::MSG_MISSING_ANALYTICS_CONFIG);
        }

        return new CurrentUser(
            $container->get(AuthorizationService::class),
            $config['auth']['user_unique_id_salt']
        );
    }

    /**
     * @deprecated can be removed following laminas v3 upgrade
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CurrentUser
     * @throws RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CurrentUser
    {
        return $this->__invoke($serviceLocator, null);
    }
}
