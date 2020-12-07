<?php

namespace Common\View\Helper;

use Laminas\I18n\View\Helper\Translate;
use Laminas\Mvc\Router\Http\RouteMatch;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\Placeholder;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Olcs\Logging\Log\Logger;

/**
 * Page Title
 */
class PageTitle extends AbstractHelper implements FactoryInterface
{
    /**
     * @var Translate
     */
    private $translator;

    /**
     * @var Placeholder
     */
    private $placeholder;

    /**
     * @var string
     */
    private $routeMatchName;

    /**
     * @var string
     */
    private $action;

    /**
     * Inject services
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->translator = $serviceLocator->get('translate');
        $this->placeholder = $serviceLocator->get('placeholder');

        /** @var RouteMatch $routeMatch */
        $routeMatch = $serviceLocator->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch();

        if ($routeMatch !== null) {
            $this->routeMatchName = $routeMatch->getMatchedRouteName();
            $this->action = $routeMatch->getParam('action');
        }

        return $this;
    }

    /**
     * Return a page title for the current page
     *
     * @return string
     */
    public function __invoke()
    {
        // get pageTitle placeholder value
        $pageTitle = (string)$this->placeholder->getContainer('pageTitle');

        if (empty($pageTitle) && !empty($this->routeMatchName)) {
            // try page title based on routing
            $pageTitleRouteKey = implode('.', array_filter(['page.title', $this->routeMatchName, $this->action]));

            if ($pageTitleRouteKey !== $this->translator->__invoke($pageTitleRouteKey)) {
                // translated value exists - use it
                $pageTitle = $pageTitleRouteKey;
            } else {
                // Log the fact that we are missing a page title
                Logger::info('Missing page title...', ['data' => ['key' => $pageTitleRouteKey]]);
            }
        }

        return $this->translator->__invoke($pageTitle);
    }
}
