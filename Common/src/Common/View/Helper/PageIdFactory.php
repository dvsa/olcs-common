<?php

namespace Common\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Router\Http\RouteMatch;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use RuntimeException;

class PageIdFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return PageId
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PageId
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        /** @var RouteMatch $routeMatch */
        $routeMatch = $container->get('Application')->getMvcEvent()->getRouteMatch();

        $routeMatchName = 'unknown';
        $action = 'unknown';

        if ($routeMatch !== null) {
            $routeMatchName = $routeMatch->getMatchedRouteName();
            $action = $routeMatch->getParam('action');
        }

        return new PageId(
            $routeMatchName,
            $action
        );
    }

    /**
     * @deprecated can be removed following laminas v3 upgrade
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CurrentUser
     * @throws RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PageId
    {
        return $this->__invoke($serviceLocator, null);
    }
}
