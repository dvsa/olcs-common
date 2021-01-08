<?php

/**
 * Page Id
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\View\Helper;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\View\Helper\HelperInterface;
use Laminas\View\Helper\AbstractHelper;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\HelperPluginManager;
use Laminas\Mvc\Router\Http\RouteMatch;

/**
 * Page Id
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PageId extends AbstractHelper implements FactoryInterface
{
    private $routeMatchName = 'unknown';

    private $action = 'unknown';

    /**
     * Create the view helper service
     *
     * @param HelperPluginManager $serviceLocator
     * @return PageId
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var RouteMatch $routeMatch */
        $routeMatch = $serviceLocator->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch();

        if ($routeMatch !== null) {
            $this->routeMatchName = $routeMatch->getMatchedRouteName();
            $this->action = $routeMatch->getParam('action');
        }

        return $this;
    }

    /**
     * Return a page id for the current page, which can be used in the automated tests
     *
     * @return string
     */
    public function __invoke()
    {
        return sprintf('pg:%s:%s', $this->routeMatchName, $this->action);
    }
}
