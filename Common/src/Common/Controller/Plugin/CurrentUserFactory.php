<?php

namespace Common\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class CurrentUserFactory
 * @package Common\Controller\Plugin
 */
final class CurrentUserFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CurrentUser
    {
        $serviceLocator = $container->getServiceLocator();
        return new CurrentUser($serviceLocator->get('ZfcRbac\Service\AuthorizationService'));
    }

    /**
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CurrentUser
    {
        return $this->__invoke($serviceLocator, CurrentUser::class);
    }
}
