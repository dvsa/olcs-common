<?php

/**
 * Generic Render View
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits;

use Zend\View\Model\ViewModel;

/**
 * Generic Render View
 *
 * - Render View logic moved here so it can be re-used without extending AbstractActionController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait GenericRenderView
{
    /**
     * Wrapper method to render a view with optional title and sub title values
     *
     * @param string|ViewModel $view
     * @param string $pageTitle
     * @param string $pageSubTitle
     *
     * @return ViewModel
     */
    protected function renderView($view, $pageTitle = null, $pageSubTitle = null)
    {
        // allow for very simple views to be passed as a string. Obviously this
        // precludes the passing of any template variables but can still come
        // in handy when no extra variables need to be set
        if (is_string($view)) {
            $viewName = $view;
            $view = new ViewModel();
            $view->setTemplate($viewName);
        }

        // no, I don't know why it's not getTerminal or isTerminal either...
        if ($view->terminate()) {
            return $view;
        }

        // allow both the page title and sub title to be passed as explicit
        // arguments to this method
        if ($pageTitle !== null) {
            $this->setPageTitle($pageTitle);
        }

        if ($pageSubTitle !== null) {
            $this->setPageSubTitle($pageSubTitle);
        }

        $viewVariables = array_merge(
            (array)$view->getVariables(),
            [
                'pageTitle' => $this->getPageTitle(),
                'pageSubTitle' => $this->getPageSubTitle()
            ]
        );

        // every page has a header, so no conditional logic needed here
        $header = new ViewModel($viewVariables);
        $header->setTemplate($this->headerViewTemplate);

        // allow a controller to specify a more specific page layout to use
        // in addition to the base one all views inherit from
        if ($this->pageLayout !== null) {
            $layout = $this->pageLayout;
            if (is_string($layout)) {
                $viewName = $layout;
                $layout = new ViewModel();
                $layout->setTemplate('layout/' . $viewName);
                $layout->setVariables($viewVariables);
            }

            $layout->addChild($view, 'content');

            // reassign the main view to be this new layout so that when we
            // come to create the base view it can just add '$view' without
            // having to care what it is
            $view = $layout;
        }

        // we always inherit from the same base layout, unless the request
        // was asynchronous in which case we render a much simpler wrapper,
        // but one which will include any inline JS we need
        // note that if templates don't want this behaviour they can either
        // mark themselves as terminal, or simply not opt-in to this helper
        $template = $this->getRequest()->isXmlHttpRequest() ? 'ajax' : 'base';
        $base = new ViewModel();
        $base->setTemplate('layout/' . $template)
            ->setTerminal(true)
            ->setVariables($viewVariables)
            ->addChild($header, 'header')
            ->addChild($view, 'content');

        return $base;
    }

    /**
     * Sets the page title
     *
     * @param array $pageTitle
     * @return $this
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
        return $this;
    }

    /**
     * Returns the page title
     *
     * @return array
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * Sets the page sub title
     *
     * @param array $pageSubTitle
     * @return $this
     */
    public function setPageSubTitle($pageSubTitle)
    {
        $this->pageSubTitle = $pageSubTitle;
        return $this;
    }

    /**
     * Returns the page sub title
     *
     * @return array
     */
    public function getPageSubTitle()
    {
        return $this->pageSubTitle;
    }
}
