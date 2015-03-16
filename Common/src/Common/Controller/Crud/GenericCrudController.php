<?php

/**
 * Generic Crud Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Crud;

use Common\Service\Table\TableBuilderAwareInterface;
use Common\Service\Table\TableBuilderAwareTrait;
use Common\Util\Redirect;
use Common\Service\Crud\CrudServiceInterface;
use Common\Service\Crud\AbstractCrudService;
use Common\Controller\Interfaces\CrudControllerInterface;
use Zend\Form\Form;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Common\Util\OptionTrait;

/**
 * Generic Crud Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class GenericCrudController extends AbstractActionController implements
    CrudControllerInterface, TableBuilderAwareInterface
{
    use OptionTrait,
        TableBuilderAwareTrait;

    /**
     * @var AbstractCrudService
     */
    protected $crudService;

    protected $translationPrefix = '';

    /**
     * @var array
     */
    protected $params;

    /**
     * Requested Name
     *
     * @var string
     */
    protected $requestedName;

    /**
     * @return mixed
     */
    public function getRequestedName()
    {
        return $this->requestedName;
    }

    /**
     * @param mixed $requestedName
     */
    public function setRequestedName($requestedName)
    {
        $this->requestedName = $requestedName;

        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Inject the crud service
     *
     * @param CrudServiceInterface $crudService
     */
    public function setCrudService(CrudServiceInterface $crudService)
    {
        $this->crudService = $crudService;
        return $this;
    }

    /**
     * Gets the crud service.
     *
     * @return AbstractCrudService
     */
    public function getCrudService()
    {
        return $this->crudService;
    }

    /**
     * Define the translation prefix
     *
     * @param string
     */
    public function setTranslationPrefix($prefix)
    {
        $this->translationPrefix = $prefix;
        return $this;
    }

    /**
     * Gets the translation prefix.
     *
     * @return string
     */
    public function getTranslationPrefix()
    {
        return $this->translationPrefix;
    }

    /**
     * Index Action
     *
     * @return mixed
     */
    public function indexAction()
    {
        $view = new ViewModel(['table' => $this->getTable()]);
        $view->setTemplate('partials/table');

        return $this->renderView($view, $this->getTranslationPrefix() . '-title');
    }

    /**
     * Returns an instantiated Table builder object.
     *
     * @return TableBuilder
     */
    public function getTable()
    {
        $data = $this->getCrudService()->getList($this->getParams());

        return $this->getTableBuilder()->buildTable($this->getOption('table'), $data);
    }

    /**
     * Sets up the config for this controller/action. FYI, called automatically by the event manager.
     *
     * @return array
     */
    public function setUpOptions()
    {
        /** @var string $requestedName contains the name of the requested controller */
        $requestedName = $this->getRequestedName();

        /** @var string $action contains current action name */
        $action = $this->params()->fromRoute('action');

        $config = $this->getServiceLocator()->get('Config');
        if (isset($config['crud_controller_config'][$requestedName][$action])) {
            $options = $config['crud_controller_config'][$requestedName][$action];

            $this->setOptions($options);
        }

        return;
    }

    /**
     * Sets up the required params for this controller as
     * specified in the config options in the module.config.php
     * crud_controller_config options array.
     *
     * FYI, This is called automatically by the factory.
     *
     * @return array
     */
    public function setUpParams()
    {
        $params = [];

        if ($requiredParams = $this->getOption('requiredParams')) {

            foreach ($requiredParams as $paramKey) {
                $params[$paramKey] = $this->params()->fromQuery($paramKey);
                $params[$paramKey] = $this->params()->fromRoute($paramKey);
            }

            $this->setParams(array_filter($params));
        }

        return;
    }

    /**
     * Sets javascript for this controller.
     *
     * FYI, the calling of this method is setup from the factory's create method
     * and therefore automatically runs on dispatch.
     *
     * @throws \Exception
     */
    public function setUpScripts()
    {
        /**
         * Contains scripts config option - this contains sub-arrays with
         * action identifying keys.
         *
         * @var array $scripts
         */
        $scripts = $this->getOption('scripts');

        /** @var string $action contains current action name */
        //$action = $this->params()->fromRoute('action');

        /** @var \Common\Service\Script\ScriptFactory $scriptService */
        $scriptService = $this->getServiceLocator()->get('Script');

        if (isset($scripts)) {
            foreach ($scripts as $scriptName) {

                /**
                 * Load the file that was specified in the action->scripts->array
                 */
                $scriptService->loadFile($scriptName);
            }
        }

        return;
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

            $processDelete = $this->getCrudService()->processDelete($id);

            if ($processDelete instanceof Redirect) {
                return $processDelete->process($this->redirect());
            }

            return $this->notFoundAction();
        }

        return $this->renderForm(
            $this->getCrudService()->getDeleteForm($request),
            $this->getTranslationPrefix() . '-delete-title',
            null,
            ['sectionText' => $this->getTranslationPrefix() . '-delete-message']
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
        $processForm = $this->getCrudService()->processForm($this->getRequest(), $id);

        // @NOTE If we have a form render it
        if ($processForm instanceof Form) {
            return $this->renderForm($processForm, $this->getTranslationPrefix() . '-form-' . $mode);
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
    /*private function renderTable()
    {
        $view = new ViewModel(['table' => $this->getCrudService()->getList($this->getParams())]);
        $view->setTemplate('partials/table');

        return $this->renderView($view, $this->getTranslationPrefix() . '-title');
    }*/

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

        if ($this->getOption('pageLayout')) {

            $layout = new ViewModel($viewVariables);
            $layout->setTemplate('layout/' . $this->getOption('pageLayout'));
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
