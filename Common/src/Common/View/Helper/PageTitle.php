<?php

namespace Common\View\Helper;

use Laminas\Form\Form;
use Laminas\I18n\View\Helper\Translate;
use Laminas\Mvc\Router\Http\RouteMatch;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\Placeholder;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Model\ViewModel;
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
     * @var ViewModel
     */
    private $viewModel;

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


        $routeMatch = $serviceLocator->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch();
        assert($routeMatch instanceof RouteMatch);

        $this->viewModel = $serviceLocator->getServiceLocator()->get('Application')->getMvcEvent()->getResult();
        assert($this->viewModel instanceof ViewModel);

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

        $prefix = $this->getPrefix() ?? "";
        $pageTitle = $this->translator->__invoke($pageTitle);

        if (!is_null($prefix)) {
            return sprintf('%s: %s', $prefix, $pageTitle);
        }

        return $pageTitle;
    }

    /**
     * Gets a prefix that are required for the title
     *
     * @return string|null
     */
    private function getPrefix(): ?string
    {
        if ($this->pageHasFormErrors()) {
            return $this->translator->__invoke("Error");
        }

        return null;
    }

    /**
     * Check if any forms on the page have errors
     *
     * @return bool
     */
    private function pageHasFormErrors(): bool
    {
        $children = $this->viewModel->getChildren();

        if (empty($children)) {
            return false;
        }

        foreach ($this->viewModel->getChildren() as $child) {
            foreach ($child->getVariables() as $key => $value) {
                if ($value instanceof Form && !empty($value->getMessages())) {
                    return true;
                }
            }
        }

        return false;
    }
}
