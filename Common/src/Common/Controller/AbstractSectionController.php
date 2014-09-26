<?php

/**
 * Abstract Section Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Controller;

use Zend\InputFilter\InputFilter;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

/**
 * Abstract Section Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractSectionController extends AbstractActionController
{
    /**
     * Holds the form name
     *
     * @var string
     */
    protected $formName;

    /**
     * Holds the form callback
     *
     * @var string
     */
    protected $formCallback = 'processSave';

    /**
     * Holds the form callback
     *
     * @var string
     */
    protected $actionFormCallback = 'processActionSave';

    /**
     * Holds the alter form method name
     *
     * @var string
     */
    protected $alterFormMethod = 'alterForm';


    /**
     * Holds the alter action form method name
     *
     * @var string
     */
    protected $alterActionFormMethod = 'alterActionForm';

    /**
     * These actions will attempt to look for a bespoke form/table/view etc
     *
     * @var array
     */
    protected $bespokeSubActions = array();

    /**
     * Here we can add sub actions that are always bespoke
     *
     * @var array
     */
    protected $defaultBespokeSubActions = array(
        'delete'
    );

    /**
     * Cache the action suffix
     *
     * @var string
     */
    protected $actionSuffix;

    /**
     * Action data map
     *
     * @var array
     */
    protected $actionDataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data'
            )
        )
    );

    /**
     * Holds the action service name
     *
     * @var string
     */
    protected $actionService = null;

    /**
     * Holds the actionDataBundle
     *
     * @var array
     */
    protected $actionDataBundle = null;

    /**
     * Holds the action data
     *
     * @var array
     */
    protected $actionData;

    /**
     * Holds the table name
     *
     * @var string
     */
    protected $tableName;

    /**
     * Holds hasTable
     *
     * @var boolean
     */
    protected $hasTable = null;

    /**
     * Holds hasForm
     *
     * @var boolean
     */
    protected $hasForm = null;

    /**
     * Current messages
     *
     * @var array
     */
    protected $currentMessages = array(
        'default' => array(),
        'error' => array(),
        'info' => array(),
        'warning' => array(),
        'success' => array()
    );

    /**
     * Get bespoke sub actions
     *
     * @return array
     */
    protected function getBespokeSubActions()
    {
        return $this->bespokeSubActions;
    }

    /**
     * Merge the defined bespokeSubActions, with the defaults
     *
     * @return array
     */
    protected function getAllBespokeSubActions()
    {
        return array_merge($this->getBespokeSubActions(), $this->defaultBespokeSubActions);
    }

    /**
     * Get form name
     *
     * @return string
     */
    protected function getFormName()
    {
        if ($this->isAction()) {

            $action = $this->getActionFromFullActionName();

            // @NOTE The delete bespoke action is a special case
            //  and we want a generic delete confirmation form
            if ($action == 'delete' && $this->isBespokeAction()) {
                return 'generic-delete-confirmation';
            }

            return $this->formName . $this->getSuffixForCurrentAction();
        }

        return $this->formName;
    }

    /**
     * Get form/table/view suffix for current action
     *
     * @return string
     */
    protected function getSuffixForCurrentAction()
    {
        if ($this->actionSuffix == null) {

            $action = $this->getActionFromFullActionName();

            $this->actionSuffix = $this->getSuffixForAction($action);
        }

        return $this->actionSuffix;
    }

    /**
     * Get suffix for action
     *
     * @param string $action
     * @return string
     */
    protected function getSuffixForAction($action)
    {
        if ($this->isBespokeAction()) {
            return '-' . $action;
        }

        return '-sub-action';
    }

    /**
     * Get the form callback
     *
     * @return string
     */
    protected function getFormCallback()
    {
        if ($this->isAction()) {
            return $this->actionFormCallback;
        }

        return $this->formCallback;
    }

    /**
     * Check if an action is bespoke
     * @return type
     */
    protected function isBespokeAction($action = null)
    {
        if ($action == null) {
            $action = $this->getActionFromFullActionName();
        }

        return in_array($action, $this->getAllBespokeSubActions());
    }

    /**
     * Determine the alter form method name
     *
     * @return string
     */
    protected function getAlterFormMethod()
    {
        if ($this->isAction()) {

            if ($this->isBespokeAction()) {
                $action = $this->getActionFromFullActionName();
                return 'alter' . ucfirst($action) . 'Form';
            }

            return $this->alterActionFormMethod;
        }

        return $this->alterFormMethod;
    }

    /**
     * Format the form table configs
     *
     * @param array $formTables
     * @return array
     */
    protected function formatFormTableConfigs($formTables)
    {
        $tableConfigs = array();

        foreach ($formTables as $table => $config) {
            $tableConfigs[$table] = array(
                'config' => $config,
                'data' => $this->getFormTableData($this->getIdentifier(), $table)
            );
        }

        return $tableConfigs;
    }

    /**
     * Moved the get form logic so it can be re-used
     *
     * @return Form
     */
    protected function getNewForm()
    {
        $formTables = $this->getFormTables();

        if ($this->isAction() || empty($formTables)) {

            return $this->generateFormWithData($this->getFormName(), $this->getFormCallback(), $this->getDataForForm());
        }

        return $this->generateTableFormWithData(
            $this->getFormName(),
            array(
                'success' => $this->getFormCallback(),
                'crud_action' => $this->getFormCallback() . 'Crud'
            ),
            $this->getDataForForm(),
            $this->formatFormTableConfigs($formTables)
        );
    }

    /**
     * Get data for form
     *
     * @return array
     */
    protected function getDataForForm()
    {
        $data = array();

        if (!$this->getRequest()->isPost()) {
            $data = $this->getFormData();
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function getFormDefaults()
    {
        return [];
    }

    /**
     * Get the last part of the action from the action name
     *
     * @param string $action
     * @return string
     */
    protected function getActionFromFullActionName($action = null)
    {
        if ($action == null) {
            $action = strtolower($this->getActionName());
        }

        return parent::getActionFromFullActionName($action);
    }

    /**
     * Get the form data
     *
     * @todo move this into section service
     *
     * @return array
     */
    protected function getFormData()
    {
        if ($this->isBespokeAction()) {

            $action = $this->getActionFromFullActionName();
            return $this->{$action . 'Load'}($this->getActionId());
        }

        if ($this->isAction()) {

            $action = $this->getActionFromFullActionName();

            if ($action === 'edit') {

                $data = $this->actionLoad($this->getActionId());
            } else {
                $data = $this->getFormDefaults();
            }

            return $this->processActionLoad($data);
        }

        return $this->processLoad($this->loadCurrent());
    }

    /**
     * Alter the form before validation
     *
     * @param Form $form
     * @return Form
     */
    protected function alterFormBeforeValidation($form)
    {
        if ($this->isAction()) {
            $action = $this->getActionFromFullActionName();

            if ($action == 'edit') {
                $form->get('form-actions')->remove('addAnother');
            }
        }

        $alterMethod = $this->getAlterFormMethod();

        if (method_exists($this, $alterMethod)) {
            return $this->$alterMethod($form);
        }

        return $form;
    }

    /**
     * Set fields as not required
     *
     * @param \Zend\InputFilter\InputFilter $inputFilter
     */
    protected function setFieldsAsNotRequired($inputFilter)
    {
        $inputs = $inputFilter->getInputs();

        foreach ($inputs as $input) {
            if ($input instanceof InputFilter) {
                $input = $this->setFieldsAsNotRequired($input);
            } else {
                $input->setRequired(false);
                $input->setAllowEmpty(true);
            }
        }

        return $inputs;
    }

    /**
     * Save the sub action
     *
     * @param array $data
     * @return array
     */
    protected function processActionSave($data, $form)
    {
        if ($this->isBespokeAction()) {
            $action = $this->getActionFromFullActionName();

            $method = $action . 'Save';
        } else {
            $data = $this->processDataMapForSave($data, $this->getActionDataMap());

            $method = 'actionSave';
        }

        if ($this->shouldSkipActionSave($data, $form)) {
            return;
        }

        $response = $this->$method($data);

        if ($response instanceof Response || $response instanceof ViewModel) {
            $this->setCaughtResponse($response);
            return;
        }

        $this->setCaughtResponse($this->postActionSave());
    }

    /**
     * Added a callback to call pre-actionSave
     *
     * @param array $data
     * @param form $form
     * @return boolean
     */
    protected function shouldSkipActionSave($data, $form)
    {
        return false;
    }

    /**
     * Post action save (Decide where to go)
     *
     * @return Response
     */
    protected function postActionSave()
    {
        if ($this->isButtonPressed('addAnother')) {
            return $this->goBackToAddAnother();
        }

        return $this->goBackToSection();
    }

    /**
     * Go back to sub action
     *
     * @return Response
     */
    protected function goBackToAddAnother()
    {
        return $this->redirect()->toRoute(null, array(), array(), true);
    }

    /**
     * Optionally add scripts to view, if there are any
     *
     * @param ViewModel $view
     */
    protected function maybeAddScripts($view)
    {
        $scripts = $this->getInlineScripts();

        if (empty($scripts)) {
            return;
        }

        // this process defers to a service which takes care of checking
        // whether the script(s) exist
        $view->setVariable('scripts', $this->loadScripts($scripts));
    }

    /**
     * Redirect to sub section
     *
     * @return Response
     */
    protected function goBackToSection()
    {
        return $this->redirect()->toRoute(
            null,
            array('action' => 'index', $this->getIdentifierName() => $this->getIdentifier())
        );
    }

    /**
     * Check for redirect
     *
     * @return Response
     */
    protected function checkForRedirect()
    {
        $crudAction = $this->checkForCrudAction();

        if ($crudAction instanceof Response || $crudAction instanceof ViewModel) {
            return $crudAction;
        }

        if ($this->isButtonPressed('cancel')) {

            $action = $this->getActionFromFullActionName();

            // This message isn't right for cancelling a delete action
            if ($action != 'delete') {

                $this->addInfoMessage('flash-discarded-changes');
            }

            return $this->goBackToSection();
        }
    }

    /**
     * Add current message
     *
     * @param string $message
     * @param string $namespace
     */
    protected function addCurrentMessage($message, $namespace = 'default')
    {
        $this->currentMessages[$namespace][] = $message;
    }

    /**
     * Attach messages to display in the current response
     */
    protected function attachCurrentMessages()
    {
        foreach ($this->currentMessages as $namespace => $messages) {
            foreach ($messages as $message) {
                $this->addMessage($message, $namespace);
            }
        }
    }

    /**
     * Add table to a view
     * @param type $view
     */
    protected function maybeAddTable($view)
    {
        if ($this->hasTable() && $view->getVariable('table') == null) {

            $tableName = $this->getTableName();

            if (!empty($tableName)) {

                $data = $this->getTableData($this->getIdentifier());

                $table = $this->alterTable($this->getTable($tableName, $data, array()));

                $view->setVariable('table', $table);
            }
        }
    }

    /**
     * Get table name
     *
     * @return string
     */
    protected function getTableName()
    {
        if ($this->isAction()) {

            return $this->tableName . $this->getSuffixForCurrentAction();
        }

        return $this->tableName;
    }

    /**
     * Check if the current sub section has a table
     *
     * @return boolean
     */
    protected function hasTable()
    {
        if (is_null($this->hasTable)) {

            $tableName = $this->getTableName();

            $this->hasTable = false;

            foreach ($this->getServiceLocator()->get('Config')['tables']['config'] as $location) {

                if (file_exists($location . $tableName . '.table.php')) {
                    $this->hasTable = true;
                    break;
                }
            }
        }

        return $this->hasTable;
    }

    /**
     * Render the section
     *
     * @return Response
     */
    protected function renderSection($view = null, $params = array())
    {
        $redirect = $this->checkForRedirect();

        if ($redirect instanceof Response || $redirect instanceof ViewModel) {
            return $redirect;
        }

        $view = $this->setupView($view, $params);

        $this->maybeAddTable($view);

        $response = $this->maybeAddForm($view);

        if ($response instanceof Response || $response instanceof ViewModel) {
            return $response;
        }

        $this->maybeAddScripts($view);

        $view = $this->preRender($view);

        $this->attachCurrentMessages();

        return $this->render($view);
    }

    /**
     * Setup the view for renderring
     *
     * @param ViewModel $view
     * @return ViewModel
     */
    protected function setupView($view = null, $params = array())
    {
        if (empty($view)) {
            $view = $this->getViewModel($params);
        }

        if ($view->getTemplate() == null) {
            $view->setTemplate($this->getViewTemplateName());
        }

        return $view;
    }

    /**
     * Potentially add a form
     *
     * @param ViewModel $view
     * @return Response
     */
    protected function maybeAddForm($view)
    {
        if ($this->hasForm() && $view->getVariable('form') == null) {

            $form = $this->getNewForm();

            $response = $this->getCaughtResponse();

            if ($response instanceof Response || $response instanceof ViewModel) {
                return $response;
            }

            $view->setVariable('form', $form);
        }
    }

    /**
     * Pre render
     *
     * @param ViewModel $view
     * @return ViewModel
     */
    protected function preRender($view)
    {
        return $view;
    }

    /**
     * Check if the current sub section has a form
     *
     * @return boolean
     */
    protected function hasForm()
    {
        if (is_null($this->hasForm)) {

            $this->hasForm = $this->formExists($this->getFormName());
        }

        return $this->hasForm;
    }

    /**
     * Delete
     *
     * @return Response
     */
    protected function delete($id = null, $service = null)
    {
        if (parent::delete($id, $service)) {

            return $this->goBackToSection();
        }

        return $this->notFoundAction();
    }

    /**
     * Getter for action data bundle
     *
     * @todo use the section services version (modified for backwards compat for the time being)
     *
     * @return array
     */
    protected function getActionDataBundle()
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getActionDataBundle();
        }

        return $this->actionDataBundle;
    }

    /**
     * Gets the action data map
     *
     * @todo use the section services version (modified for backwards compat for the time being)
     *
     * @return array
     */
    protected function getActionDataMap()
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getActionDataMap();
        }

        return $this->actionDataMap;
    }

    /**
     * Default method to get form table data
     *
     * @param int $id
     * @param string $table
     * @return array
     */
    protected function getFormTableData($id, $table)
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->getFormTableData($id, $table);
        }

        return array();
    }

    /**
     * Simple helper method to load the current record
     *
     * @todo Move load into section service
     *
     * @return array
     */
    protected function loadCurrent()
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->load($this->getIdentifier());
        }

        return $this->load($this->getIdentifier());
    }

    /**
     * Alter the form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterForm($form)
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->alterForm($form);
        }

        return $form;
    }

    /**
     * Alter the action form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterActionForm($form)
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->alterActionForm($form);
        }

        return $form;
    }

    /**
     * Load sub section data
     *
     * @param int $id
     * @return array
     */
    protected function actionLoad($id)
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->actionLoad($id);
        }

        if ($this->actionData === null) {

            $this->actionData = $this->makeRestCall(
                $this->getActionService(),
                'GET',
                $id,
                $this->getActionDataBundle()
            );
        }

        return $this->actionData;
    }

    /**
     * Save sub action data
     *
     * @param array $data
     */
    protected function actionSave($data, $service = null)
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->actionSave($data, $service);
        }

        if (is_null($service)) {
            $service = $this->getActionService();
        }

        return $this->save($data, $service);
    }

    /**
     * Alter delete form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterDeleteForm($form)
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->alterDeleteForm($form);
        }

        return $form;
    }

    /**
     * Default Load data for the delete confirmation form
     *
     * @param int $id
     * @return array
     */
    protected function deleteLoad($id)
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->deleteLoad($id);
        }

        if (is_array($id)) {
            $id = implode(',', $id);
        }

        return array('data' => array('id' => $id));
    }

    /**
     * Default Process delete
     *
     * @param array $data
     */
    protected function deleteSave($data)
    {
        if ($this->getSectionServiceName() !== null) {
            $this->getSectionService()->deleteSave($data);
        } else {
            $ids = explode(',', $data['data']['id']);

            foreach ($ids as $id) {
                $this->delete($id, $this->getActionService());
            }
        }

        return $this->goBackToSection();
    }

    /**
     * Alter table
     *
     * This method should be overridden if alterations are required
     *
     * @param object $table
     * @return object
     */
    protected function alterTable($table)
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->alterTable($table);
        }

        return $table;
    }

    /**
     * Process loading the sub section data
     *
     * @param array $data
     * @return array
     */
    protected function processActionLoad($data)
    {
        if ($this->getSectionServiceName() !== null) {
            return $this->getSectionService()->processActionLoad($data);
        }

        return $data;
    }
}
