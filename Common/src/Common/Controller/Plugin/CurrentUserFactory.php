<?php

namespace Common\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CurrentUserFactory
 * @package Common\Controller\Plugin
 */
final class CurrentUserFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return CurrentUserInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        return new CurrentUser($serviceLocator->get('ZfcRbac\Service\AuthorizationService'));
    }
}
