<?php

namespace Common\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\Router\Http\RouteMatch;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use RuntimeException;

class PageTitleFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return PageTitle
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PageTitle
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        $viewHelperManager = $container->get('ViewHelperManager');
        $translator = $viewHelperManager->get('translate');
        $placeholder = $viewHelperManager->get('placeholder');

        /** @var RouteMatch $routeMatch */
        $routeMatch = $container->get('Application')->getMvcEvent()->getRouteMatch();

        $routeMatchName = 'unknown';
        $action = 'unknown';

        if ($routeMatch !== null) {
            $routeMatchName = $routeMatch->getMatchedRouteName();
            $action = $routeMatch->getParam('action');
        }

        return new PageTitle($translator, $placeholder, $routeMatchName, $action);
    }

    /**
     * @deprecated can be removed following laminas v3 upgrade
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CurrentUser
     * @throws RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PageTitle
    {
        return $this->__invoke($serviceLocator, null);
    }
}
