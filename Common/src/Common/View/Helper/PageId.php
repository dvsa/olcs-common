<?php

/**
 * Page Id
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\View\Helper\HelperInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;
use Zend\Mvc\Router\Http\RouteMatch;

/**
 * Page Id
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PageId extends AbstractHelper implements FactoryInterface
{
    private $routeMatchName;

    private $action;

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
        $this->routeMatchName = $routeMatch->getMatchedRouteName();
        $this->action = $routeMatch->getParam('action');

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
