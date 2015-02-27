<?php

/**
 * Generic Crud Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Crud;

use Common\Util\Redirect;
use Common\Service\Crud\CrudServiceInterface;
use Common\Controller\Interfaces\CrudControllerInterface;
use Zend\Form\Form;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Generic Crud Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class GenericCrudController extends AbstractActionController implements CrudControllerInterface
{
    protected $crudService;

    protected $translationPrefix = '';

    protected $pageLayout = 'admin-layout';

    /**
     * Inject the crud service
     *
     * @param CrudServiceInterface $crudService
     */
    public function setCrudService(CrudServiceInterface $crudService)
    {
        $this->crudService = $crudService;
    }

    /**
     * Define the translation prefix
     *
     * @param string
     */
    public function setTranslationPrefix($prefix)
    {
        $this->translationPrefix = $prefix;
    }

    /**
     * Override the page layout
     *
     * @param string $layout
     */
    public function setPageLayout($layout = null)
    {
        $this->pageLayout = $layout;
    }

    /**
     * Index Action
     *
     * @return mixed
     */
    public function indexAction()
    {
        $this->getServiceLocator()->get('Script')->loadFile('table-actions');

        return $this->renderTable();
    }

    /**
     * Add action
     *
     * @return mixed
     */
    public function addAction()
    {
        return $this->addOrEditForm('add');
    }

    /**
     * Edit action
     *
     * @return mixed
     */
    public function editAction()
    {
        return $this->addOrEditForm('edit', $this->params('id', 0));
    }

    /**
     * Delete action
     *
     * @return mixed
     */
    public function deleteAction()
    {
        return $this->confirmDelete($this->params('id', 0));
    }

    /**
     * Handle confirm delete functionality
     *
     * @param int $id
     * @return mixed
     */
    private function confirmDelete($id)
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $id = explode(',', $id);

            $processDelete = $this->crudService->processDelete($id);

            if ($processDelete instanceof Redirect) {
                return $processDelete->process($this->redirect());
            }

            return $this->notFoundAction();
        }

        return $this->renderForm(
            $this->crudService->getDeleteForm($request),
            $this->translationPrefix . '-delete-title',
            null,
            ['sectionText' => $this->translationPrefix . '-delete-message']
        );
    }

    /**
     * Common behaviour between add and edit forms
     *
     * @param string $mode
     * @param int $id
     * @return mixed
     */
    private function addOrEditForm($mode, $id = null)
    {
        $processForm = $this->crudService->processForm($this->getRequest(), $id);

        // @NOTE If we have a form render it
        if ($processForm instanceof Form) {
            return $this->renderForm($processForm, $this->translationPrefix . '-form-' . $mode);
        }

        // If we have a Redirect object, process the redirect
        if ($processForm instanceof Redirect) {
            return $processForm->process($this->redirect());
        }

        return $this->notFoundAction();
    }

    /**
     * Create the table view for a crud page
     *
     * @return ViewModel
     */
    private function renderTable()
    {
        $view = new ViewModel(['table' => $this->crudService->getList()]);
        $view->setTemplate('partials/table');

        return $this->renderView($view, $this->translationPrefix . '-title');
    }

    /**
     * Create a form view for a crud table
     *
     * @param Form $form
     * @param string $title
     * @param string $subTitle
     * @param array $params
     * @return ViewModel
     */
    private function renderForm(Form $form, $title = null, $subTitle = null, $params = [])
    {
        $view = new ViewModel(array_merge($params, ['form' => $form]));
        $view->setTemplate('partials/form');

        return $this->renderView($view, $title, $subTitle);
    }

    /**
     * Render a view
     *
     * @param ViewModel $view
     * @param string $pageTitle
     * @param string $pageSubTitle
     * @return ViewModel
     */
    private function renderView(ViewModel $view, $pageTitle = null, $pageSubTitle = null)
    {
        $viewVariables = array_merge(
            (array)$view->getVariables(),
            [
                'pageTitle' => $pageTitle,
                'pageSubTitle' => $pageSubTitle
            ]
        );

        $header = new ViewModel($viewVariables);
        $header->setTemplate('partials/header');

        if ($this->pageLayout !== null) {

            $layout = new ViewModel($viewVariables);
            $layout->setTemplate('layout/' . $this->pageLayout);
            $layout->addChild($view, 'content');

            $view = $layout;
        }

        $template = $this->getRequest()->isXmlHttpRequest() ? 'ajax' : 'base';

        $base = new ViewModel($viewVariables);
        $base->setTemplate('layout/' . $template)
            ->setTerminal(true)
            ->addChild($header, 'header')
            ->addChild($view, 'content');

        return $base;
    }
}
