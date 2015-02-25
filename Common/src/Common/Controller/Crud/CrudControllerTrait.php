<?php

/**
 * Crud Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Crud;

use Zend\Form\Form;
use Zend\View\Model\ViewModel;
use Common\Util\Redirect;
use Common\Service\Table\TableBuilder;

/**
 * Crud Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait CrudControllerTrait
{
    protected function addOrEditForm($crudService, $pageTitle, $id = null)
    {
        $processForm = $crudService->processForm($this->getRequest(), $id);

        // @NOTE If we have a form render it
        if ($processForm instanceof Form) {
            return $this->renderForm($processForm, $pageTitle);
        }

        // If we have a Redirect object, process the redirect
        if ($processForm instanceof Redirect) {
            return $processForm->process($this->redirect());
        }

        return $this->notFoundAction();
    }

    /**
     * The current page's extra layout, over and above the
     * standard base template
     *
     * @var string
     */
    protected $pageLayout = 'wide-layout';

    /**
     * Render a table within the admin area
     *
     * @param TableBuilder $table
     * @param string $title
     * @param string $subTitle
     * @return ViewModel
     */
    protected function renderTable(TableBuilder $table, $title = null, $subTitle = null)
    {
        $view = new ViewModel(['table' => $table]);
        $view->setTemplate('partials/table');

        return $this->renderView($view, $title, $subTitle);
    }

    /**
     * Render a form within the admin area
     *
     * @param Form $form
     * @param string $title
     * @param string $subTitle
     * @return ViewModel
     */
    protected function renderForm(Form $form, $title = null, $subTitle = null)
    {
        $view = new ViewModel(['form' => $form]);
        $view->setTemplate('partials/form');

        return $this->renderView($view, $title, $subTitle);
    }

    /**
     * Wrapper method to render a view with optional title and sub title values
     *
     * @param ViewModel $view
     * @param string $pageTitle
     * @param string $pageSubTitle
     *
     * @return ViewModel
     */
    protected function renderView(ViewModel $view, $pageTitle = null, $pageSubTitle = null)
    {
        if ($view->terminate()) {
            return $view;
        }

        $viewVariables = array_merge(
            (array)$view->getVariables(),
            [
                'pageTitle' => $pageTitle,
                'pageSubTitle' => $pageSubTitle
            ]
        );

        // every page has a header, so no conditional logic needed here
        $header = new ViewModel($viewVariables);
        $header->setTemplate('partials/header');

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
}
