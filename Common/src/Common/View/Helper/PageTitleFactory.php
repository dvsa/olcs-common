<?php

namespace Common\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\Router\Http\RouteMatch;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
}
